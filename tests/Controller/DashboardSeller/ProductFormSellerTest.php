<?php

namespace App\Tests\Controller\DashboardSeller;

use App\Entity\Product;
use App\Entity\User;
use App\Tests\Controller\CreatesProductTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductFormSellerTest extends WebTestCase
{
    use CreatesProductTrait;

    private KernelBrowser $client;
    private User $seller;
    /** @var string[] fichiers créés pendant le test (photos envoyées) */
    private array $files = [];

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->seller = $this->createUser('Flora', ['ROLE_SELLER']);
        $this->client->loginUser($this->seller);
    }

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            @unlink($file);
        }
        parent::tearDown();
    }

    public function testNewFormIsInFrenchWithVatRatesAndPricePreview(): void
    {
        $this->createCategory('Robes');

        $this->client->request('GET', '/seller/products/new');

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame('Ajouter un produit | Le comptoire des couturiers');
        self::assertSelectorTextContains('label[for="product_name"]', 'Nom du produit');
        self::assertSelectorExists('select#product_category option:contains("Robes")');
        self::assertSelectorExists('select#product_vatRate option[data-rate="20"][selected]');
        self::assertSelectorExists('select#product_vatRate option[data-rate="5.5"]');
        self::assertSelectorExists('[data-controller="product-form"] [data-product-form-target="priceInclVat"]');
        self::assertSelectorTextContains('.form-actions button[type="submit"]', 'Créer le produit');
    }

    public function testEmptyFormShowsErrorsInsteadOfCrashing(): void
    {
        $crawler = $this->client->request('GET', '/seller/products/new');
        $this->client->submit($crawler->filter('form[name="product"]')->form([
            'product[name]' => '',
            'product[priceExclVat]' => '',
            'product[description]' => '',
        ]));

        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('form', 'Donnez un nom à votre produit.');
        self::assertSelectorTextContains('form', 'Indiquez un prix.');
        self::assertSelectorTextContains('form', 'Décrivez votre produit');
        self::assertSelectorTextContains('form', 'Choisissez une catégorie.');
        self::assertSame(0, $this->countSellerProducts());
    }

    public function testNegativePriceIsRejected(): void
    {
        $category = $this->createCategory();
        $crawler = $this->client->request('GET', '/seller/products/new');
        $this->client->submit($crawler->filter('form[name="product"]')->form(
            $this->validData($category->getId(), ['product[priceExclVat]' => '-5'])
        ));

        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('form', 'Le prix doit être supérieur à 0.');
    }

    public function testCreateProductWithPhoto(): void
    {
        $category = $this->createCategory();
        $crawler = $this->client->request('GET', '/seller/products/new');
        $form = $crawler->filter('form[name="product"]')->form($this->validData($category->getId()));
        $form['product[images]'][0]->upload($this->makePng('robe.png'));
        $this->client->submit($form);

        self::assertResponseRedirects('/seller/products');
        $product = $this->findCreated('Robe portefeuille');
        self::assertSame($this->seller->getId(), $product->getUser()->getId());
        self::assertSame($category->getId(), $product->getCategory()->getId());
        self::assertSame(5.5, $product->getVatRate());
        self::assertCount(1, $product->getImages());
        $stored = $this->uploadDir().'/'.$product->getImages()[0];
        $this->files[] = $stored;
        self::assertFileExists($stored);

        $this->client->followRedirect();
        self::assertSelectorTextContains('.seller-products-table', 'Robe portefeuille');
    }

    public function testFileThatIsNotAnImageIsRejected(): void
    {
        $category = $this->createCategory();
        $fake = tempnam(sys_get_temp_dir(), 'img');
        file_put_contents($fake, 'pas une image');

        $crawler = $this->client->request('GET', '/seller/products/new');
        $form = $crawler->filter('form[name="product"]')->form($this->validData($category->getId()));
        $form['product[images]'][0]->upload(new UploadedFile($fake, 'faux.png', 'image/png', null, true));
        $this->client->submit($form);

        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('form', 'Veuillez télécharger une photo valide (PNG ou JPEG).');
        self::assertSame(0, $this->countSellerProducts());
    }

    public function testEditKeepsExistingPhotosAndAddsNewOnes(): void
    {
        $product = $this->createProduct('Robe de Flora', 76, 20, $this->seller);
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->find(Product::class, $product->getId())->setImages(['ancienne.png']);
        $em->flush();

        $crawler = $this->client->request('GET', '/seller/products/'.$product->getId().'/edit');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Modifier « Robe de Flora »');
        self::assertSelectorExists('img[src="/uploads/productImages/ancienne.png"]');
        // Le formulaire de suppression ne doit pas être imbriqué dans le formulaire produit.
        self::assertSelectorNotExists('form[name="product"] form');
        self::assertSelectorExists('.form-card--danger form[action="/seller/products/'.$product->getId().'"]');

        $form = $crawler->filter('form[name="product"]')->form(['product[name]' => 'Robe de Flora (lin)']);
        $form['product[images]'][0]->upload($this->makePng('nouvelle.png'));
        $this->client->submit($form);

        self::assertResponseRedirects('/seller/products');
        $em->clear();
        $updated = $em->find(Product::class, $product->getId());
        self::assertSame('Robe de Flora (lin)', $updated->getName());
        self::assertCount(2, $updated->getImages());
        self::assertSame('ancienne.png', $updated->getImages()[0]);
        $this->files[] = $this->uploadDir().'/'.$updated->getImages()[1];
        self::assertFileExists($this->uploadDir().'/'.$updated->getImages()[1]);
    }

    private function validData(int $categoryId, array $overrides = []): array
    {
        return $overrides + [
            'product[name]' => 'Robe portefeuille',
            'product[category]' => (string) $categoryId,
            'product[description]' => 'Robe en lin lavé, coupe portefeuille, lavable à 30 °C.',
            'product[priceExclVat]' => '80',
            'product[vatRate]' => '5.5',
        ];
    }

    private function makePng(string $name): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'png');
        $img = imagecreatetruecolor(10, 10);
        imagepng($img, $path);

        return new UploadedFile($path, $name, 'image/png', null, true);
    }

    private function uploadDir(): string
    {
        return static::getContainer()->getParameter('kernel.project_dir').'/public/uploads/productImages';
    }

    private function findCreated(string $name): Product
    {
        $product = static::getContainer()->get(EntityManagerInterface::class)
            ->getRepository(Product::class)->findOneBy(['name' => $name]);
        self::assertNotNull($product);
        $this->created[] = $product; // nettoyage après le test (Doctrine ordonne les suppressions)

        return $product;
    }

    private function countSellerProducts(): int
    {
        return static::getContainer()->get(EntityManagerInterface::class)
            ->getRepository(Product::class)->count(['user' => $this->seller->getId()]);
    }
}

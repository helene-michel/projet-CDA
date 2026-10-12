import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    static targets = ["tabsNav", "tabPanel", "btnPanel"]
    connect() {
       
    }
    setTab(event){
        const elm = event.target;

            
            for(const btnElm of this.btnPanelTarget.children){
              
                if(elm === btnElm){
                    elm.classList.add('active');
                    const panel = document.getElementById(event.params.id);
                    panel.classList.add('active');
                }else{
                    btnElm.classList.remove('active');
                    const panel = document.getElementById(btnElm.getAttribute('data-navigation-id-param'));
                    panel.classList.remove('active');
                }
            };
     

    }
    
}

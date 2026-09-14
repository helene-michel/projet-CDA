import { Controller } from '@hotwired/stimulus';


export default class extends Controller {

    static targets = [ "megaMenu" ]

    show(){
        if(this.megaMenuTarget.getAttribute("hidden")){
            this.megaMenuTarget.removeAttribute("hidden")
        }else{
            this.megaMenuTarget.setAttribute("hidden","hidden")
        }   
    
    }


}

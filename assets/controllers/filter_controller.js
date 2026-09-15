import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    static values = {
        route:String
    }
    connect() {

    }
    filterTag(event){

        const elm = event.target;
        const id = elm.getAttribute("data-id");
        this.sendFilter(id);
        console.log(id);
    }
    async sendFilter(id){
        const url = new URL(this.routeValue);
        url.searchParams.append('tag', id);
     try{
         const response = await fetch(url,{
             method:"GET",
         });
         const data = await response.json();
         console.log(data);
     }   catch(error){
         console.log(error);
     }
    }
}

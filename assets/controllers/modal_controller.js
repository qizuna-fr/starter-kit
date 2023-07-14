import {Controller} from "@hotwired/stimulus";
import {useClickOutside, useDebounce, useHotkeys, useTransition} from "stimulus-use";

export default class extends Controller {

    static targets = ["window" , "content"]

    connect() {
        console.log("modal controller connected")

    }

    open(e){

        if(e.params.url !== null){
            fetch(e.params.url)
                .then(res => res.text())
                .then(d => this.contentTarget.innerHTML = d);
        }

        const modal = e.params.name
        const myModal = this.windowTargets.filter( w => {
            return w.classList.contains(modal)
        })[0] ?? null

        myModal.classList.toggle('hidden')

    }

    close(e){
        const modal = e.params.name
        const myModal = this.windowTargets.filter( w => {
            return w.classList.contains(modal)
        })[0] ?? null

        myModal.classList.toggle('hidden')
    }

}

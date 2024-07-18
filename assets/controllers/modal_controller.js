import {Controller} from "@hotwired/stimulus";
import {useClickOutside, useDebounce, useHotkeys, useTransition} from "stimulus-use";
import {toggle} from  'el-transition';

export default class extends Controller {

    static targets = ["window" , "content" , 'container']

    open(e){

        e.preventDefault();

        const linkHref = e.currentTarget.getAttribute('href')
        const pathParameter = e.params.url;
        const url = linkHref ? linkHref : pathParameter;
        const modalName = e.params.name


        const myModal = this.windowTargets.filter( w => {
            return w.classList.contains('modal_container')
        })[0] ?? null

        // verifier si on ouvre une modale qui existe deja
        const myModalByName = this.windowTargets.filter( w => {
            return w.classList.contains('modal') && w.classList.contains(modalName)
        })[0] ?? null

        //si une modale designee existe, on l'ouvre en priorite.
        if(myModalByName !== null){
            toggle(myModalByName)
            return
        }

        // if there is no url then we show only the modal without loading anything into it
        if(url !== undefined){
            fetch(url)
                .then(res => res.text())
                .then(d => this.containerTarget.innerHTML = d);
        }

        const modal = this.containerTarget.querySelector('.modal_container')
        toggle(modal)

    }

    close(e){

        //on verifie si on a une modale avec un nom defini
        const modalName = e.params.name;
        const namedModal = this.windowTargets.filter( w => {
            return w.classList.contains(modalName)
        })[0] ?? undefined

        //si la modale est designee par son nom c'est elle qu'on traite en priorite.
        if(namedModal !== undefined){
            toggle(namedModal);
            return
        }

        const modalContainer = e.currentTarget.closest('[data-modal-name]');
        toggle(modalContainer);
    }

}

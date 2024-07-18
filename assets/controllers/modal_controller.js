import { Controller } from "@hotwired/stimulus";
import { useClickOutside, useDebounce, useHotkeys, useTransition } from "stimulus-use";
import { toggle } from 'el-transition';

export default class extends Controller {
    static targets = ["window", "content", 'container'];

    connect() {
        this.handleKeydown = this.handleKeydown.bind(this);
    }

    open(e) {
        e.preventDefault();

        const linkHref = e.currentTarget.getAttribute('href');
        const pathParameter = e.params.url;
        const url = linkHref ? linkHref : pathParameter;
        const modalName = e.params.name;

        const myModal = this.windowTargets.filter(w => {
            return w.classList.contains('modal_container');
        })[0] ?? null;

        // verifier si on ouvre une modale qui existe deja
        const myModalByName = this.windowTargets.filter(w => {
            return w.classList.contains('modal') && w.classList.contains(modalName);
        })[0] ?? null;

        //si une modale designee existe, on l'ouvre en priorite.
        if (myModalByName !== null) {
            toggle(myModalByName);
            document.addEventListener('keydown', this.handleKeydown);
            return;
        }

        // if there is no url then we show only the modal without loading anything into it
        if (url !== undefined) {
            fetch(url)
                .then(res => res.text())
                .then(d => this.containerTarget.innerHTML = d);
        }

        const modal = this.containerTarget.querySelector('.modal_container');
        toggle(modal);

        document.addEventListener('keydown', this.handleKeydown);
    }

    close(e) {

        const myModal = this.element.querySelector('.modal_container:not(.hidden), .modal:not(.hidden)');
        toggle(myModal)

        document.removeEventListener('keydown', this.handleKeydown);

    }

    handleKeydown(event) {
        if (event.key === 'Escape') {
            this.close({ params: {} });
        }
    }
}

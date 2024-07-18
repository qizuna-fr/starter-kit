import {Controller} from "@hotwired/stimulus";
import axios from 'axios';
import {enter, leave} from 'el-transition';

export default class extends Controller {

    static targets = ['main' , 'blur' , 'panel' , 'content' , 'spinner']

    connect() {
        this.handleKeydown = this.handleKeydown.bind(this);
    }

    open(event) {

        event.preventDefault();

        const linkHref = event.currentTarget.getAttribute('href')
        const pathParameter = event.params.path;
        const url = linkHref ? linkHref : pathParameter;

        axios.get(url).then(response => {
            this.contentTarget.innerHTML = response.data
            //this.spinnerTarget.classList.add('hidden')
            enter(this.mainTarget)
            enter(this.blurTarget)
            enter(this.panelTarget)

            document.addEventListener('keydown', this.handleKeydown);
        });
    }

    close() {
        leave(this.blurTarget)
        leave(this.panelTarget)
        leave(this.mainTarget)

        document.removeEventListener('keydown', this.handleKeydown);

    }

    handleKeydown(event) {
        if (event.key === 'Escape') {
            this.close();
        }
    }

}

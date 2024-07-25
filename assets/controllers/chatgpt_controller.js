import {Controller} from "@hotwired/stimulus";
import {enter} from 'el-transition';

export default class extends Controller {

    static targets = ['container', 'response', 'phrase']

    static values = {
        url: String
    }

    connect() {
        this.element.addEventListener('submit', this.handleSubmit.bind(this));
    }

    async handleSubmit(e) {

        e.preventDefault()

        const formData = new FormData();
        formData.append('phrase', this.phraseTarget.value);

        const response = await fetch(this.urlValue, {
            method: 'POST',
            body: formData
        });

        if (response.ok) {
            this.responseTarget.innerHTML = await response.text();
            enter(this.containerTarget)
        } else {
            console.error('Error:', response.statusText);
        }


        // this.responseTarget.innerHTML =  "Auf der Heide blüht ein kleines Blümelein\n" +
        //     "Und das heißt, Erika\n" +
        //     "\n" +
        //     "Heiß von hunderttausend kleinen Bienelein\n" +
        //     "\n" +
        //     "Wird umschwärmt, Erika"


    }


}


import {Controller} from "@hotwired/stimulus";
import {enter, leave} from 'el-transition';

export default class extends Controller {

    static targets = ['container' , 'backdrop', 'menu']

    connect() {

    }


    close() {

        Promise.all([leave(this.backdropTarget), leave(this.menuTarget)]).then(() => {
            leave(this.containerTarget)
        });


    }

    open() {
        enter(this.containerTarget)
        enter(this.menuTarget)
        enter(this.backdropTarget)
    }

}

//container

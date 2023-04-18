
import {ClickOutsideController, useClickOutside, useTransition} from "stimulus-use";

export default class extends ClickOutsideController {

    static targets = ['menu' ]

    connect() {
        // useClickOutside(this ,{onlyVisible:true })
        // useTransition(this, {
        //     element: this.menuTarget,
        //     enterActive: 'transition ease-out duration-100',
        //     enterFrom: 'transform opacity-0 scale-95',
        //     enterTo: 'transform opacity-100 scale-100',
        //     leaveActive: 'transition ease-in duration-75',
        //     leaveFrom: '"transform opacity-100 scale-100',
        //     leaveTo: 'transform opacity-0 scale-95',
        //     hiddenClass: 'hidden',
        //     // set this, because the item *starts* in an open state
        //     transitioned: false,
        // });
    }

    // clickOutside(event) {
    //     console.log('clickOUtside')
    //     event.preventDefault()
    //     this.leave()
    // }
    //
    // close() {
    //     console.log('close')
    //     this.leave();
    // }
    //
    // open() {
    //     console.log('open')
    //     this.enter();
    // }
    //
    // toggle() {
    //     console.log('toggle')
    //     this.toggleTransition();
    // }

}

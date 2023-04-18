import {Controller} from "@hotwired/stimulus";
import {useClickOutside, useDebounce, useHotkeys, useTransition} from "stimulus-use";

export default class extends Controller {

    static targets = ['container' , 'initial' , 'empty' , 'choice' , 'input' , 'paletteInput' , 'palette']

    static debounces = [
        {
            name: 'search',
            wait: 300
        }
    ]

    connect() {
        useTransition(this,{
            element: this.containerTarget,
            enterActive: 'ease-in duration-100',
            enterFrom: 'opacity-0',
            enterTo: 'opacity-100',
            leaveActive: 'ease-in duration-100',
            leaveFrom: 'opacity-100',
            leaveTo: 'opacity-0',
            hiddenClass: 'hidden',
            // set this, because the item *starts* in an open state
            transitioned: false,
        })

        useDebounce(this)

        useHotkeys(this, {
            "cmd+k": [this.open],
            "esc": [this.close]
        });

        useClickOutside(this , {
            element: this.paletteTarget,
            events: ['mousedown'],
            onlyVisible: true
        })

        this.inputTarget.addEventListener('focus' , () =>{this.open()})
        this.paletteInputTarget.addEventListener('keyup' , (e) => this.search(e))
    }

    async search(e){

        if(e.target.value === ""){
            this.showInitial()
            return
        }

        const res = await fetch('/search?q='+e.target.value)
        const data = await res.text()

        if(data){
            this.choiceTarget.innerHTML = data
            this.showResults()
        } else {
            this.showEmpty()
        }
    }

    clickOutside(e){
        e.preventDefault()
        this.close()
    }

    close(){
        this.paletteInputTarget.value = '';
        this.showInitial()
        this.leave()
    }

    open() {
        this.enter()
        this.paletteInputTarget.focus()
    }

    toggle() {
        this.toggleTransition()
    }

    showEmpty(){
        this.initialTarget.classList.add('hidden')
        this.emptyTarget.classList.remove('hidden')
        this.choiceTarget.classList.add('hidden')
    }

    showResults(){
        this.initialTarget.classList.add('hidden')
        this.emptyTarget.classList.add('hidden')
        this.choiceTarget.classList.remove('hidden')
    }

    showInitial(){
        this.initialTarget.classList.remove('hidden')
        this.emptyTarget.classList.add('hidden')
        this.choiceTarget.classList.add('hidden')
    }

}

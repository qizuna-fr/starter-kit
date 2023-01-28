import {Controller} from "@hotwired/stimulus";

export default class extends Controller {

    static targets = [ 'tabContent' , 'tab' ]

    connect() {

        this.currentTab = this.tabContentTarget.dataset.name

        this.tabContentTargets.forEach( item => item.classList.add('hidden'))
        this.tabContentTarget.classList.remove('hidden')

        this.tabTarget.classList.add('active')

        window.addEventListener('hashchange' , (e) => this.onHashChange() );

        this.tabOnLoad = window.location.hash.slice(1);
        if(this.tabOnLoad !== ""){
            this.handleHashOnPageLoad(this.tabOnLoad)
        }
    }

    disconnect(){
        window.removeEventListener('hashchange')
    }

    onHashChange(e) {
        const newTab = window.location.hash.slice(1)

        if(newTab === this.currentTab){
            return
        }
        this.setCurrentTab(newTab);
        this.handleTabVisibility(this.currentTab);
    }


    setCurrentTab(newTab) {
        this.currentTab = newTab
    }

    handleHashOnPageLoad(hash){

        if(hash === this.currentTab){
            return
        }

        this.currentTab = hash
        this.handleTabVisibility(this.currentTab);

    }


    handleTabVisibility(currentTab) {
        this.tabContentTargets.map(item => {
            item.dataset.tabName === currentTab ? item.classList.remove('hidden') : item.classList.add('hidden')
        })

        this.tabTargets.map(item => {
            const tabHash = item.href.split('#')[1] ?? null
            tabHash === currentTab ? item.classList.add('active') : item.classList.remove('active')
        })
    }


}

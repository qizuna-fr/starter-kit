import { Controller } from "@hotwired/stimulus"
import zxcvbn from "zxcvbn"

export default class extends Controller {
    static targets = ["password", "strength"]

    connect() {
        console.log(this.passwordTarget, this.strengthTarget)
        this.updateStrength()
    }

    updateStrength() {
        const password = this.passwordTarget.value
        const strength = zxcvbn(password)
        let strengthText = 'Faible'
        let strengthColorClass = 'text-red-500'
        switch (strength.score) {
            case 0:
            case 1:
                strengthText = 'Faible'
                strengthColorClass = 'red'
                break;
            case 2:
                strengthText = 'Moyen'
                strengthColorClass = 'orange'
                break;
            case 3:
                strengthText = 'Bon'
                strengthColorClass = '#278F3D'
                break;
            case 4:
                strengthText = 'Excellent'
                strengthColorClass  = '#32A49A'
                break;
        }
        this.strengthTarget.style.color = strengthColorClass
        this.strengthTarget.textContent = `Force du mot de passe : ${strengthText}`
    }
}

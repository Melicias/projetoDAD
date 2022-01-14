<template>
    <div>
        <h2>My Profile</h2>
        <div class="row" style="margin-top:60px;">
            <div class="col-sm-4" style="text-align:left">
                <div>
                    <h5>Phone Number:&nbsp;</h5> 
                    <h4><b>{{ $store.state.user.phone_number }}</b></h4>
                    <h5>Username:&nbsp;</h5>
                    <h4><b>{{ $store.state.user.name }}</b></h4>    
                    <h5>Email:&nbsp;</h5>
                    <h4><b>{{ $store.state.user.email }}</b></h4>
                    <h5>Balance:&nbsp;</h5>
                    <h4><b>{{ $store.state.user.balance }}€</b></h4>
                    <h5>Valor máximo que o Vcard suporta:&nbsp;</h5>
                    <h4><b>{{ $store.state.user.max_debit }}€</b></h4>
                    <h5>Valor do PiggyBank:&nbsp;</h5>
                    <h4><b>{{ $store.state.user.piggy_bank }}€</b></h4>
                </div>
                <br>
                <button class="btn btn-default size margin" @click="openManagePiggyBank" v-show="btnPiggyBank">Gerir PiggyBank</button>
                <br>
                <button class="btn btn-default size margin" @click="openProfileSettings" v-show="btnProfileSettings">Defenições de Perfil</button>
                <br>
                <button class="btn btn-default size" @click="openDeleteVcard" v-show="btnDeleteVcard">Apagar Vcard</button>
            </div>
            <div class="col-sm-4" style="text-align:left">
                <div v-show="piggyBank">
                    <h4>Gestão do PiggyBank</h4>
                    <label>Valor a transferir para o PiggyBank:</label>
                    <br><input class="form-control margin" type="number" placeholder="0.00€" v-model="amount">
                    <!--<label>Password:</label>
                    <br><input class="form-control margin" type="text" placeholder="password" v-model="validations.password">
                    <label>Código de confirmação:</label>
                    <br><input class="form-control margin" id="inputCode" name="code" size="1" maxlength="4" placeholder="****" v-model="validations.confirmation_code"> -->
                    <div v-if="valueErrors != null">
                        <small>{{showValueErrors}}</small><br>
                    </div>
                    <div style="text-align:center;">
                        <button class="btn btn-default sizePiggi" @click="cancelPiggyBank">Voltar</button>
                        <button class="btn btn-default sizePiggi" @click="addToPiggyBank">Adicionar</button>
                        <button class="btn btn-default sizePiggi" @click="removeFromPiggyBank">Remover</button>    
                    </div>
                </div>
                <profile-settings v-show="profileSettingsActivation"></profile-settings>
                <div v-show="deleteInputs">
                    <h4>Apagar Vcard</h4>
                    <br><input class="form-control margin" type="text" placeholder="password" v-model="validations.password">
                    <input class="form-control margin" id="inputCode" name="code" size="1" maxlength="4" placeholder="****" v-model="validations.confirmation_code">
                    <div v-if="deleteErrors != null">
                        <small>{{showDeleteErrors}}</small><br>
                    </div>
                    <button class="btn btn-default size" @click="cancelDelete">Cancelar</button>
                    <button class="btn btn-default size" @click="deleteVcard">Apagar</button>  
                </div>
            </div> 
        </div>
    </div>
</template>


<script>
import ProfileSettings from "./ProfileSettings.vue"

export default {
    name: 'Main',
    components: {
        ProfileSettings
    },
    data (){
        return {
            profileSettingsActivation: false,
            deleteInputs: false,
            piggyBank: false,
            btnProfileSettings: true,
            btnDeleteVcard: true,
            btnPiggyBank: true,
            amount: null,
            phone_number: null,
            validations : {
                password: null,
                confirmation_code: null
            },
            deleteErrors: null,
            valueErrors: null
        }
    },
    computed: {
        showDeleteErrors(){
            return this.deleteErrors
        },
        showValueErrors(){
            return this.valueErrors
        }
    },
    methods: {
        openProfileSettings(){
            this.profileSettingsActivation = true
            this.btnProfileSettings = false
            this.btnDeleteVcard = false
            this.btnPiggyBank = false
            this.emitter.on("closeSettings", () => {
                this.profileSettingsActivation = false
                this.btnProfileSettings = true
                this.btnDeleteVcard = true
                this.btnPiggyBank = true
            })
        },
        openDeleteVcard(){
            this.deleteInputs = true
            this.btnDeleteVcard = false
            this.btnProfileSettings = false
            this.btnPiggyBank = false
        },
        openManagePiggyBank(){
            this.piggyBank = true
            this.btnDeleteVcard = false
            this.btnProfileSettings = false
            this.btnPiggyBank = false
        },
        deleteVcard(){
            if(confirm("Are you sure that you want to delete your Vcard?")){
                this.$axios.delete('vcards', 
                {
                    headers: this.validations
                })
                .then((response) => {
                    console.log(response)
                    this.$toast.success('Vcard Deleted with success')
                    this.emitter.emit("finishSession")
                })
                .catch((error) => {
                    console.log(error.response)
                    this.getDeleteErrors(error)
                    this.$toast.error('Was not possible to delete the Vcard!')
                })
            }
        },
        addToPiggyBank(){
            if(confirm("Are you sure that you want to add money to your PiggyBank?")){
                this.$axios.post('piggybank/add', { amount: this.amount })
                .then((response) => {
                    console.log(response)
                    this.cleanPiggyBankFieldAndErrors()
                    this.$toast.success('Money added to PiggyBank with success!')
                    this.$store.dispatch("fetchUserProfile");
                })
                .catch((error) => {
                    console.log(error.response)
                    this.getValueErrors(error)
                    this.$toast.error('Was not possible to add money to your PiggyBank.')
                })
            }
        },
        removeFromPiggyBank(){
            if(confirm("Are you sure that you want to remove money from your PiggyBank?")){
                this.$axios.post('piggybank/remove', 
                { 
                    amount: this.amount
                })
                .then((response) => {
                    console.log(response)
                    this.cleanPiggyBankFieldAndErrors()
                    this.$toast.success('Money removed from your PiggyBank with success!')
                    this.$store.dispatch("fetchUserProfile");
                })
                .catch((error) => {
                    console.log(error.response)
                    this.getValueErrors(error)
                    this.$toast.error('Was not possible to remove money from your PiggyBank.')
                })
            }
        },
        cancelDelete(){
            this.deleteInputs = false
            this.showButtonsActions()
            this.cleanDeleteFieldsAndErrors()
        },
        cancelPiggyBank(){
            this.piggyBank = false
            this.showButtonsActions()
        },
        showButtonsActions(){
            this.btnDeleteVcard = true
            this.btnProfileSettings = true
            this.btnPiggyBank = true
        },
        getDeleteErrors(error) {
            if (error.response.data.message) {
                this.deleteErrors = "The password or code are not correct!";
            }
        },
        getValueErrors(error) {
            if (error.response.data.error) {
                this.valueErrors = error.response.data.error;
            }
        },
        cleanPiggyBankFieldAndErrors(){
            this.amount = null
            this.valueErrors = null
        },
        cleanDeleteFieldsAndErrors() {
            this.validations.password = null
            this.validations.confirmation_code = null
            this.deleteErrors = null
        }
    },
    beforeMount() {

    },
}
</script>

<style scoped>
.size{
  width: 160px;  
}
.sizePiggi{
  width: 90px;  
}
.margin{
    margin-bottom: 5px;
}
</style>
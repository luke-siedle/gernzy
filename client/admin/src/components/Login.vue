<template>
  <div class="uk-box-shadow-small gernzy-login uk-padding">
    <form @submit="checkForm">
      <div class="uk-alert uk-alert-danger uk-margin-top" v-if="errors.length">
        <p  v-for="(error, key) in errors" :key="key">{{ error }}</p>
      </div>
      <div class="uk-margin-top">
        <label for="email">
          Username
        </label>
        <input id="email" type="text" placeholder="Email" v-model="email" class="uk-input">
      </div>
      <div class="uk-margin-top">
        <label for="password">
          Password
        </label>
        <input id="password" type="password" placeholder="Password" v-model="password" class="uk-input">
      </div>
      <div class="uk-margin-top">
        <Button text="Sign in" type="submit" class="uk-button uk-button-primary" />
      </div>
    </form>
    <p>
      &copy;2019 Gernzy. All rights reserved
    </p>
  </div>
</template>

<script>

  import { mapState } from 'vuex'
  import gql from 'graphql-tag'
  import Button from './Button.vue'

  export default {
    components: {
      Button,
    },
    data: () => ({
      email: '',
      password: '',
      errors: [],
    }),
    computed: mapState({
      name: state => state.session.name
    }),
    methods: {
      checkForm: async function ( event ){
        event.preventDefault();
        const { email, password } = this
        if( !email || !password ){
          this.errors = [
            'Please complete your email and password'
          ]
          return
        }

        this.errors = []

        try {
          const result = await this.$apollo.mutate({
            mutation: gql`mutation ($email: String!, $password: String!) {
              logIn( input: {
                email: $email,
                password: $password
              }){
                user { id }
                token
              }
            }`,
            // Parameters
            variables: {
              email,
              password,
            },
          })

          const { errors } = this.$store.dispatch('logIn', result)
          if( errors ){
            this.errors = errors
          }
        } catch(e){
          console.log(e)
        }
      }
    }
  }

</script>

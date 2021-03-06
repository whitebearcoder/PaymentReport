<template>
  <b-container fluid class="root-container">
    <top-navbar></top-navbar>
    <b-form class="login-signup-form" @submit.prevent="onSubmit">
      <h1 class="text-center mb-2">Add a user</h1>
      <b-form-group
        id="register-username"
        label="Username"
        label-for="input-username"
        :invalid-feedback="errors.first('username')"
        :state="!(submitted && errors.has('username'))"
      >
        <b-form-input
          id="input-username"
          name="username"
          v-model="formData.username"
          v-validate="`required`"
          placeholder="Enter username"
        ></b-form-input>
      </b-form-group>
      <b-form-group
        id="register-password"
        label="Password"
        label-for="input-password"
        :invalid-feedback="errors.first('password')"
        :state="!(submitted && errors.has('password'))"
      >
        <b-form-input
          id="input-password"
          name="password"
          v-model="formData.password"
          v-validate="`required|min:8|verify_password`"
          placeholder="Enter password"
          type="password"
        ></b-form-input>
      </b-form-group>

      <b-form-group
        label="User role"
        label-for="user-type"
        :invalid-feedback="errors.first('user_type')"
        :state="!(submitted && errors.has('user_type'))"
      >
        <b-form-select id="user-type" v-model="formData.user_type" required>
          <option disabled :selected="!formData.user_type" value=""
            >Select a user type</option
          >
          <option :value="userType.VIEW" :key="userType.VIEW">{{
            userType.VIEW
          }}</option>
          <option :value="userType.INSERT" :key="userType.INSERT">{{
            userType.INSERT
          }}</option>
        </b-form-select>
      </b-form-group>

      <div class="d-flex justify-content-end mt-4">
        <b-button variant="primary" class="ml-3" type="submit"
          >Create User</b-button
        >
      </div>
    </b-form>
  </b-container>
</template>

<script>
import Swal from 'sweetalert2';
import { USER_TYPE } from '@/constants';
import { mapGetters } from 'vuex';
import TopNavbar from '@/sharedComponents/top-navbar.vue';
import { REGISTER_USER } from '@/utils/validators';

export default {
  name: 'register-modal',
  components: {
    Swal,
    TopNavbar
  },
  data() {
    return {
      submitted: false,
      userType: USER_TYPE,
      formData: {
        username: '',
        password: '',
        user_type: ''
      }
    };
  },
  computed: {
    ...mapGetters(['currentUser', 'check'])
  },

  created() {
    if (this.currentUser.user_type !== USER_TYPE.ADMIN) this.$router.push('/');
    this.$validator.localize('en', REGISTER_USER.en);
  },
  methods: {
    async onSubmit(event) {
      event.preventDefault();
      this.submitted = true;

      Swal.fire({
        title: 'Are you sure?',
        text: `Create user ${this.formData.username} as ${this.formData.user_type}`,
        showCancelButton: true
      }).then(res => {
        if (res.isConfirmed) {
          this.$validator.validateAll().then(async valid => {
            if (valid) {
              const loader = this.$loading.show();
              try {
                let res = await axios.post('/api/create-user', this.formData);
                if (res && res.data) {
                  Swal.fire({
                    title: `User ${this.formData.user_type} Created`,
                    icon: 'success'
                  });

                  this.formData = {
                    username: '',
                    password: '',
                    user_type: ''
                  };
                  this.submitted = false;
                }
              } catch (err) {
                if (err.response.status === 409) {
                  this.$validator.errors.add({
                    field: err.response.data.error.field,
                    msg: err.response.data.error.msg
                  });
                } else {
                  Swal.fire({
                    title: 'User Create Failed',
                    icon: 'error'
                  });
                }
              }
              loader.hide();
            }
          });
        }
      });
    }
  }
};
</script>

<style scoped>
.login-signup-form {
  margin: 100px auto;
}
</style>

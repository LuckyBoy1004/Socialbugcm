<template>
  <div>
    <div v-if="display && sub.id">
      <form id="configuration_form" class="defaultForm form-horizontal socialbugcrm" :action="'index.php?controller=AdminModules&configure=socialbugcrm&token=' + tkn" method="post" enctype="multipart/form-data">
        <input type="hidden" name="submitsocialbugcrm" value="1">
        <div class="panel" id="fieldset_0">
          <div class="panel-footer">
            <button type="submit" value="1" id="configuration_form_submit_btn" name="submitsocialbugcrm" class="btn btn-default pull-left">
              <i class="process-icon-refresh"></i> Launch App
            </button>
          </div>
        </div>
      </form>
    </div>
    <prestashop-accounts>
    </prestashop-accounts>
    <ps-billing-customer
        v-if="billingContext.user.email"
        ref="psBillingCustomerRef"
        :context="billingContext"
        :onOpenModal="openBillingModal"
        :onEventHook="eventHookHandler"
    />
    <ps-billing-modal
      v-if="modalType !== ''"
      :context="billingContext"
      :type="modalType"
      :onCloseModal="closeBillingModal"
      :onEventHook="eventHookHandler"
    />
    <modal-container
      v-if="modalType !== ''"
      :context="billingContext"
      :type="modalType"
      :onCloseModal="closeModal"
      :onEventHook="eventHookHandler"
    />
  </div>
</template>
<script>
  import  Vue  from "vue";
  import { EVENT_HOOK_TYPE } from '@prestashopcorp/billing-cdc/dist/bundle.umd';
  import {
    CustomerComponent,
    ModalContainerComponent,
  } from "@prestashopcorp/billing-cdc/dist/bundle.umd";

  export default {
    components: {
      PsBillingCustomer: CustomerComponent.driver("vue", Vue),
      PsBillingModal: ModalContainerComponent.driver("vue", Vue),
    },
    data() {
      return {
        billingContext: { ...window.storePsSocialbugcrm.context.psBillingContext.context },
        modalType: "",
        sub: null,
        tkn: window.token,
        display: false
      };
    },
    // ...
    created() {
    },
    // ...
    provide() {
      return {
        emailSupport: window.psBillingContext.context.emailSupport,
      };
    },
    methods: {
      openBillingModal(type, data) {
        this.modalType = type;
        this.billingContext = { ...this.billingContext, ...data };
      },
      closeBillingModal(data) {
        this.modalType = "";
        this.$refs.psBillingCustomerRef.parent.updateProps({
          context: {
            ...this.billingContext,
            ...data,
          },
        });
      },
      eventHookHandler(type, data) {
        console.log('type, data: ', type, data);
        switch (type) {
          case EVENT_HOOK_TYPE.BILLING_INITIALIZED:
            // data structure is: { customer, subscription }
            this.sub = data.subscription;
            break;
          case EVENT_HOOK_TYPE.SUBSCRIPTION_UPDATED:
            // data structure is: { customer, subscription, card }
            this.sub = data.subscription;
            break;
          case EVENT_HOOK_TYPE.SUBSCRIPTION_CANCELLED:
            // data structure is: { customer, subscription }
            this.sub = data.subscription;
            break;
        }
        this.display = data.subscription.status === "active" ? true : false
      },
    },
    mounted() {
      window?.psaccountsVue?.init() || require('prestashop_accounts_vue_components').init();
      this.$refs.psBillingCustomerRef.parent.emit("init:billing", {
        CREATE_SUBSCRIPTION: {
          // This is the id of your free plan
          planId: "default-free",
        },
      });
    }
  };
</script>

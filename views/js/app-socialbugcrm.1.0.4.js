(function(){"use strict";var t={601:function(t,n,e){var o=e(538),i=function(){var t=this,n=t._self._c;return n("div",[t.display&&t.sub.id?n("div",[n("form",{staticClass:"defaultForm form-horizontal socialbugcrm",attrs:{id:"configuration_form",action:"index.php?controller=AdminModules&configure=socialbugcrm&token="+t.tkn,method:"post",enctype:"multipart/form-data"}},[n("input",{attrs:{type:"hidden",name:"submitsocialbugcrm",value:"1"}}),t._m(0)])]):t._e(),n("prestashop-accounts"),t.billingContext.user.email?n("ps-billing-customer",{ref:"psBillingCustomerRef",attrs:{context:t.billingContext,onOpenModal:t.openBillingModal,onEventHook:t.eventHookHandler}}):t._e(),""!==t.modalType?n("ps-billing-modal",{attrs:{context:t.billingContext,type:t.modalType,onCloseModal:t.closeBillingModal,onEventHook:t.eventHookHandler}}):t._e(),""!==t.modalType?n("modal-container",{attrs:{context:t.billingContext,type:t.modalType,onCloseModal:t.closeModal,onEventHook:t.eventHookHandler}}):t._e()],1)},r=[function(){var t=this,n=t._self._c;return n("div",{staticClass:"panel",attrs:{id:"fieldset_0"}},[n("div",{staticClass:"panel-footer"},[n("button",{staticClass:"btn btn-default pull-left",attrs:{type:"submit",value:"1",id:"configuration_form_submit_btn",name:"submitsocialbugcrm"}},[n("i",{staticClass:"process-icon-refresh"}),t._v(" Launch App ")])])])}],l=e(738),s={components:{PsBillingCustomer:l.CustomerComponent.driver("vue",o.ZP),PsBillingModal:l.ModalContainerComponent.driver("vue",o.ZP)},data(){return{billingContext:{...window.storePsSocialbugcrm.context.psBillingContext.context},modalType:"",sub:null,tkn:window.token,display:!1}},created(){},provide(){return{emailSupport:window.psBillingContext.context.emailSupport}},methods:{openBillingModal(t,n){this.modalType=t,this.billingContext={...this.billingContext,...n}},closeBillingModal(t){this.modalType="",this.$refs.psBillingCustomerRef.parent.updateProps({context:{...this.billingContext,...t}})},eventHookHandler(t,n){switch(console.log("type, data: ",t,n),t){case l.EVENT_HOOK_TYPE.BILLING_INITIALIZED:this.sub=n.subscription;break;case l.EVENT_HOOK_TYPE.SUBSCRIPTION_UPDATED:this.sub=n.subscription;break;case l.EVENT_HOOK_TYPE.SUBSCRIPTION_CANCELLED:this.sub=n.subscription;break}this.display="active"===n.subscription.status}},mounted(){window?.psaccountsVue?.init()||e(654).init(),this.$refs.psBillingCustomerRef.parent.emit("init:billing",{CREATE_SUBSCRIPTION:{planId:"default-free"}})}},a=s,u=e(1),c=(0,u.Z)(a,i,r,!1,null,null,null),p=c.exports;o.ZP.config.productionTip=!1,new o.ZP({render:t=>t(p)}).$mount("#app")}},n={};function e(o){var i=n[o];if(void 0!==i)return i.exports;var r=n[o]={exports:{}};return t[o].call(r.exports,r,r.exports,e),r.exports}e.m=t,function(){var t=[];e.O=function(n,o,i,r){if(!o){var l=1/0;for(c=0;c<t.length;c++){o=t[c][0],i=t[c][1],r=t[c][2];for(var s=!0,a=0;a<o.length;a++)(!1&r||l>=r)&&Object.keys(e.O).every((function(t){return e.O[t](o[a])}))?o.splice(a--,1):(s=!1,r<l&&(l=r));if(s){t.splice(c--,1);var u=i();void 0!==u&&(n=u)}}return n}r=r||0;for(var c=t.length;c>0&&t[c-1][2]>r;c--)t[c]=t[c-1];t[c]=[o,i,r]}}(),function(){e.d=function(t,n){for(var o in n)e.o(n,o)&&!e.o(t,o)&&Object.defineProperty(t,o,{enumerable:!0,get:n[o]})}}(),function(){e.g=function(){if("object"===typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(t){if("object"===typeof window)return window}}()}(),function(){e.o=function(t,n){return Object.prototype.hasOwnProperty.call(t,n)}}(),function(){var t={143:0};e.O.j=function(n){return 0===t[n]};var n=function(n,o){var i,r,l=o[0],s=o[1],a=o[2],u=0;if(l.some((function(n){return 0!==t[n]}))){for(i in s)e.o(s,i)&&(e.m[i]=s[i]);if(a)var c=a(e)}for(n&&n(o);u<l.length;u++)r=l[u],e.o(t,r)&&t[r]&&t[r][0](),t[r]=0;return e.O(c)},o=self["webpackChunksocialbugcrm"]=self["webpackChunksocialbugcrm"]||[];o.forEach(n.bind(null,0)),o.push=n.bind(null,o.push.bind(o))}();var o=e.O(void 0,[998],(function(){return e(601)}));o=e.O(o)})();
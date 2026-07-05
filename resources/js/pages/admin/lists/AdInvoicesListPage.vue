<template>
  <AdminCrudPage :config="config" />
</template>

<script>
import AdminCrudPage from '../../../components/shared/AdminCrudPage.vue';
import { resourceConfigs } from '../config/resourceConfigs';

export default {
  name: 'AdInvoicesListPage',
  components: { AdminCrudPage },
  computed: {
    config() {
      return resourceConfigs['ad-invoices'];
    },
  },
};
</script>

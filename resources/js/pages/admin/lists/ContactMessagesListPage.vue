<template>
  <AdminCrudPage :config="config" />
</template>

<script>
import AdminCrudPage from '../../../components/shared/AdminCrudPage.vue';
import { resourceConfigs } from '../config/resourceConfigs';

export default {
  name: 'ContactMessagesListPage',
  components: { AdminCrudPage },
  computed: {
    config() {
      return resourceConfigs['contact-messages'];
    },
  },
};
</script>

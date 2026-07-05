<template>
  <AdminCrudPage :config="config" />
</template>

<script>
import AdminCrudPage from '../../../components/shared/AdminCrudPage.vue';
import { resourceConfigs } from '../config/resourceConfigs';

export default {
  name: 'MassGuidesListPage',
  components: { AdminCrudPage },
  computed: {
    config() {
      return resourceConfigs['mass-guides'];
    },
  },
};
</script>

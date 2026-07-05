<template>
  <div v-if="visibleDonations.length" class="donation-widget" :class="{ compact }">
    <h4 v-if="!compact" class="widget-title">Support Us</h4>
    <h5 v-else class="widget-title">Donate</h5>
    <div v-for="item in visibleDonations" :key="item.id" class="donation-item">
      <strong>{{ item.label }}</strong>
      <p v-if="item.type === 'bank'" class="mb-1 small">
        {{ item.bank_name }}<br>
        <template v-if="item.account_number">
          {{ item.account_name }} — {{ item.account_number }}
        </template>
        <template v-else>
          {{ item.account_name }}
        </template>
      </p>
      <p v-else-if="item.type === 'paypal' && item.paypal_email" class="mb-1 small">{{ item.paypal_email }}</p>
      <p v-else-if="item.type === 'ewallet'" class="mb-1 small">
        <template v-if="item.ewallet_number">
          {{ item.ewallet_provider }} — {{ item.ewallet_number }}
        </template>
        <template v-else>
          {{ item.ewallet_provider }}
        </template>
      </p>
      <div v-if="item.qr_code_path" class="donation-qr">
        <img :src="item.qr_code_path" :alt="`${item.label} QR code`" class="donation-qr-img">
      </div>
      <div v-if="item.instructions" class="small text-muted mb-2 donation-instructions" v-html="item.instructions" />
    </div>
    <router-link v-if="compact" to="/donate" class="btn btn-sm btn-donate w-100 mt-2">Learn More</router-link>
  </div>
</template>

<script>
export default {
  name: 'DonationWidget',
  props: {
    donations: { type: Array, default: () => [] },
    compact: { type: Boolean, default: false },
    location: { type: String, default: null },
  },
  computed: {
    visibleDonations() {
      if (!this.location) {
        return this.donations;
      }

      return this.donations.filter((item) => {
        const locations = item.display_locations || [];
        return locations.includes(this.location);
      });
    },
  },
};
</script>

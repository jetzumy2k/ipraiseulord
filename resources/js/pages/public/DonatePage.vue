<template>
  <div class="public-page">
    <h1 class="page-title">Donate</h1>
    <p class="lead">Your generosity helps us share the Gospel and maintain this ministry.</p>

    <div v-if="loading" class="text-center py-5">
      <i class="fas fa-spinner fa-spin fa-2x" />
    </div>

    <div v-else-if="!donationsEnabled" class="content-card p-4 text-muted">
      Donations are not available at this time. Please check back later or contact us for other ways to support the ministry.
    </div>

    <div v-else-if="donations.length" class="row g-4">
      <div v-for="item in donations" :key="item.id" class="col-md-6">
        <div class="content-card donation-card">
          <div class="donation-card-body">
            <div class="donation-card-details">
              <h3>{{ item.label }}</h3>
              <template v-if="item.type === 'bank'">
                <p><strong>Bank:</strong> {{ item.bank_name }}</p>
                <p><strong>Account Name:</strong> {{ item.account_name }}</p>
                <p><strong>Account Number:</strong> {{ item.account_number }}</p>
              </template>
              <template v-else-if="item.type === 'paypal'">
                <p><strong>PayPal:</strong> {{ item.paypal_email }}</p>
              </template>
              <template v-else-if="item.type === 'ewallet'">
                <p><strong>{{ item.ewallet_provider }}:</strong> {{ item.ewallet_number }}</p>
              </template>
              <div
                v-if="item.instructions"
                class="text-muted mb-0 donation-instructions"
                v-html="item.instructions"
              />
            </div>
            <div v-if="item.qr_code_path" class="donation-qr donation-qr--page">
              <img :src="item.qr_code_path" :alt="`${item.label} QR code`" class="donation-qr-img">
              <p class="small text-muted mb-0 mt-2">Scan to donate</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <p v-else class="text-muted mt-4">
      Donation options will appear here once configured by the administrator.
    </p>
  </div>
</template>

<script>
export default {
  name: 'DonatePage',
  data() {
    return {
      allDonations: [],
      donationsEnabled: true,
      loading: true,
    };
  },
  computed: {
    donations() {
      return this.allDonations.filter((item) => {
        const locations = item.display_locations || [];
        return locations.includes('donation_page');
      });
    },
  },
  mounted() {
    window.axios.get('/public/settings')
      .then(({ data }) => {
        this.donationsEnabled = data.donations_enabled !== false;
        this.allDonations = data.donations || [];
      })
      .finally(() => { this.loading = false; });
  },
};
</script>

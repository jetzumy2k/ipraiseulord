export const pageRouteOptions = [
  { value: '', label: 'None (standalone page — About, Privacy, etc.)' },
  { value: 'home', label: 'Home' },
  { value: 'donate', label: 'Donate' },
  { value: 'contact', label: 'Contact' },
  { value: 'bible', label: 'Holy Bible' },
  { value: 'mass-guide', label: 'Mass Guide' },
  { value: 'fiesta-calendar', label: 'Fiesta Calendar' },
  { value: 'novenas', label: 'Novenas' },
  { value: 'prayers', label: 'Prayers' },
  { value: 'ai-advice', label: 'AI Advice' },
];

export const pageRouteLabels = Object.fromEntries(
  pageRouteOptions.filter((option) => option.value).map((option) => [option.value, option.label]),
);

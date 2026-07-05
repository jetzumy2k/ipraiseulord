const LITURGICAL_THEMES = {
  green: {
    className: 'liturgical-theme-green',
    label: 'Green',
  },
  white: {
    className: 'liturgical-theme-white',
    label: 'White',
  },
  purple: {
    className: 'liturgical-theme-purple',
    label: 'Purple',
  },
  violet: {
    className: 'liturgical-theme-purple',
    label: 'Purple',
  },
  red: {
    className: 'liturgical-theme-red',
    label: 'Red',
  },
  rose: {
    className: 'liturgical-theme-rose',
    label: 'Rose',
  },
  pink: {
    className: 'liturgical-theme-rose',
    label: 'Rose',
  },
  gold: {
    className: 'liturgical-theme-gold',
    label: 'Gold',
  },
  black: {
    className: 'liturgical-theme-black',
    label: 'Black',
  },
};

export function normalizeLiturgicalColor(color) {
  if (!color || typeof color !== 'string') {
    return null;
  }

  return color.toLowerCase().trim().replace(/\s+/g, ' ');
}

export function liturgicalTheme(color) {
  const normalized = normalizeLiturgicalColor(color);

  if (!normalized) {
    return null;
  }

  if (LITURGICAL_THEMES[normalized]) {
    return LITURGICAL_THEMES[normalized];
  }

  const matchedKey = Object.keys(LITURGICAL_THEMES).find((key) => normalized.includes(key));

  return matchedKey ? LITURGICAL_THEMES[matchedKey] : null;
}

export function liturgicalThemeClass(color) {
  return liturgicalTheme(color)?.className || '';
}

/** @deprecated Use liturgicalThemeClass */
export function liturgicalHeaderClass(color) {
  return liturgicalThemeClass(color);
}

export const shareNetworks = [
  { id: 'facebook', label: 'Facebook', icon: 'fab fa-facebook-f', color: '#1877f2' },
  { id: 'twitter', label: 'X', icon: 'fab fa-x-twitter', color: '#000000' },
  { id: 'whatsapp', label: 'WhatsApp', icon: 'fab fa-whatsapp', color: '#25d366' },
  { id: 'telegram', label: 'Telegram', icon: 'fab fa-telegram', color: '#0088cc' },
  { id: 'copy', label: 'Copy', icon: 'fas fa-link', color: '#5c2e2e' },
];

export function buildSharePayload({ title, text, url }) {
  const pageUrl = url || window.location.href;
  const shareTitle = title || document.title;
  const shareText = text || shareTitle;

  return {
    title: shareTitle,
    text: shareText,
    url: pageUrl,
    combined: `${shareText}\n\n${pageUrl}`.trim(),
  };
}

export function shareToNetwork(networkId, payload) {
  const { title, text, url, combined } = buildSharePayload(payload);
  const encodedUrl = encodeURIComponent(url);
  const encodedText = encodeURIComponent(text);
  const encodedCombined = encodeURIComponent(combined);

  const urls = {
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}&quote=${encodedText}`,
    twitter: `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedText}`,
    whatsapp: `https://wa.me/?text=${encodedCombined}`,
    telegram: `https://t.me/share/url?url=${encodedUrl}&text=${encodedText}`,
  };

  if (networkId === 'copy') {
    return copyShareText(combined);
  }

  if (networkId === 'native' && navigator.share) {
    return navigator.share({ title, text, url }).catch(() => false);
  }

  const shareUrl = urls[networkId];
  if (!shareUrl) {
    return false;
  }

  window.open(shareUrl, '_blank', 'noopener,noreferrer,width=640,height=480');
  return true;
}

export async function copyShareText(text) {
  try {
    await navigator.clipboard.writeText(text);
    return true;
  } catch {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.setAttribute('readonly', '');
    textarea.style.position = 'absolute';
    textarea.style.left = '-9999px';
    document.body.appendChild(textarea);
    textarea.select();
    const copied = document.execCommand('copy');
    document.body.removeChild(textarea);
    return copied;
  }
}

export async function nativeShare(payload) {
  if (!navigator.share) {
    return copyShareText(buildSharePayload(payload).combined);
  }

  const { title, text, url } = buildSharePayload(payload);

  try {
    await navigator.share({ title, text, url });
    return true;
  } catch {
    return false;
  }
}

export function buildBibleVerseShareText({ bookName, chapter, version, verses }) {
  const sorted = [...verses].sort((a, b) => a.verse_number - b.verse_number);
  const first = sorted[0]?.verse_number;
  const last = sorted[sorted.length - 1]?.verse_number;
  const reference = first === last
    ? `${bookName} ${chapter}:${first}`
    : `${bookName} ${chapter}:${first}-${last}`;

  const body = sorted
    .map((verse) => `"${verse.text.trim()}"`)
    .join('\n\n');

  return {
    title: `${reference} (${version})`,
    text: `${body}\n\n— ${reference} (${version})`,
    reference,
  };
}

export function bibleVerseShareUrl(path, verses) {
  const base = `${window.location.origin}${path}`;
  if (!verses?.length) {
    return base;
  }

  const numbers = verses.map((v) => v.verse_number).sort((a, b) => a - b);
  const hash = numbers.length === 1
    ? `#v${numbers[0]}`
    : `#v${numbers[0]}-v${numbers[numbers.length - 1]}`;

  return `${base}${hash}`;
}

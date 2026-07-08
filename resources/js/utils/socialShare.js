export const shareNetworks = [
  { id: 'facebook', label: 'Facebook', icon: 'fab fa-facebook-f', color: '#1877f2' },
  { id: 'twitter', label: 'X', icon: 'fab fa-x-twitter', color: '#000000' },
  { id: 'whatsapp', label: 'WhatsApp', icon: 'fab fa-whatsapp', color: '#25d366' },
  { id: 'telegram', label: 'Telegram', icon: 'fab fa-telegram', color: '#0088cc' },
  { id: 'copy', label: 'Copy', icon: 'fas fa-link', color: '#5c2e2e' },
];

let sharePopup = null;
let shareInFlight = false;

export function buildSharePayload({ title, text, url }) {
  const pageUrl = (url != null && url !== '') ? url : window.location.href;
  const shareTitle = title || document.title;
  const shareText = (text || '').trim() || shareTitle;

  return {
    title: shareTitle,
    text: shareText,
    url: pageUrl,
    combined: `${shareText}\n\n${pageUrl}`.trim(),
  };
}

function truncateForTweet(text, url, maxLength = 280) {
  const suffix = `\n\n${url}`;
  const available = maxLength - suffix.length - 1;

  if (available <= 0 || text.length <= available) {
    return text;
  }

  return `${text.slice(0, Math.max(available, 0)).trim()}…`;
}

function openSharePopup(targetUrl) {
  const windowName = 'praiseulord-social-share';

  if (sharePopup && !sharePopup.closed) {
    sharePopup.location.href = targetUrl;
    sharePopup.focus();
    return true;
  }

  sharePopup = window.open(targetUrl, windowName, 'width=640,height=480,scrollbars=yes,resizable=yes');

  if (!sharePopup) {
    window.location.assign(targetUrl);
    return true;
  }

  sharePopup.focus();
  return true;
}

export function shareToNetwork(networkId, payload) {
  if (shareInFlight) {
    return false;
  }

  const { title, text, url, combined } = buildSharePayload(payload);
  const encodedUrl = encodeURIComponent(url);
  const encodedText = encodeURIComponent(text);
  const encodedCombined = encodeURIComponent(combined);
  const tweetText = truncateForTweet(text, url);

  const urls = {
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,
    twitter: `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodeURIComponent(tweetText)}`,
    whatsapp: `https://wa.me/?text=${encodedCombined}`,
    telegram: `https://t.me/share/url?url=${encodedUrl}&text=${encodedText}`,
  };

  if (networkId === 'copy') {
    return copyShareText(combined);
  }

  if (networkId === 'native' && navigator.share) {
    shareInFlight = true;
    return navigator.share({ title, text, url })
      .then(() => true)
      .catch(() => false)
      .finally(() => {
        window.setTimeout(() => {
          shareInFlight = false;
        }, 500);
      });
  }

  const shareUrl = urls[networkId];
  if (!shareUrl) {
    return false;
  }

  shareInFlight = true;
  openSharePopup(shareUrl);
  window.setTimeout(() => {
    shareInFlight = false;
  }, 800);

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

export function buildSidebarQuoteShareText({ title, text, reference, version }) {
  const ref = version ? `${reference} (${version})` : reference;

  return {
    title: title ? `${title} — ${reference}` : reference,
    text: `"${String(text).trim()}"\n\n— ${ref}`,
  };
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

export function formatShareReference(ref) {
  if (typeof ref === 'string') {
    return ref;
  }

  const reference = ref.reference || '';
  const version = ref.version ? ` (${ref.version})` : '';

  return `${reference}${version}`.trim() || ref.text || '';
}

export function buildAiAdviceShareUrl(conversationId) {
  const origin = typeof window !== 'undefined' ? window.location.origin : '';

  if (!conversationId) {
    return `${origin}/ai-advice`;
  }

  const id = encodeURIComponent(String(conversationId));

  return `${origin}/ai-advice/${id}?id=${id}`;
}

export function buildAiAdviceShareText({ question, answerSections, answer, references }) {
  const answerBody = answerSections?.length
    ? answerSections.map((section) => section.text).filter(Boolean).join('\n\n')
    : (answer || '').trim();

  let text = `Question: ${question.trim()}\n\nAnswer: ${answerBody}`;

  const seen = new Set();
  const refs = (references || [])
    .map(formatShareReference)
    .filter((ref) => {
      const key = ref.toLowerCase();
      if (!ref || seen.has(key)) {
        return false;
      }
      seen.add(key);
      return true;
    });

  if (refs.length) {
    text += `\n\nScripture References:\n${refs.join('\n')}`;
  }

  return {
    title: 'AI Spiritual Advice',
    text,
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

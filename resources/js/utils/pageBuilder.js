let blockId = 0;

export function createBlockId() {
  blockId += 1;
  return `block-${Date.now()}-${blockId}`;
}

export function createBlock(type, data = {}) {
  const defaults = {
    heading: { type: 'heading', level: 2, text: '' },
    paragraph: { type: 'paragraph', html: '' },
    quote: { type: 'quote', html: '' },
    list: { type: 'list', ordered: false, items: [''] },
    image: { type: 'image', url: '', alt: '', caption: '' },
    separator: { type: 'separator' },
    columns: { type: 'columns', leftHtml: '', rightHtml: '' },
  };

  const block = { id: createBlockId(), ...(defaults[type] || defaults.paragraph) };

  return { ...block, ...data, id: block.id, type: type || block.type };
}

export function htmlToBlocks(html) {
  if (!html || !html.trim()) {
    return [createBlock('paragraph')];
  }

  const doc = new DOMParser().parseFromString(html, 'text/html');
  const blocks = [];

  doc.body.childNodes.forEach((node) => {
    if (node.nodeType !== 1) {
      return;
    }

    const tag = node.tagName.toLowerCase();

    if (/^h[1-6]$/.test(tag)) {
      blocks.push(createBlock('heading', {
        level: Number(tag.slice(1)),
        text: node.textContent?.trim() || '',
      }));
      return;
    }

    if (tag === 'p') {
      blocks.push(createBlock('paragraph', { html: node.innerHTML || '' }));
      return;
    }

    if (tag === 'blockquote') {
      blocks.push(createBlock('quote', { html: node.innerHTML || '' }));
      return;
    }

    if (tag === 'ul' || tag === 'ol') {
      blocks.push(createBlock('list', {
        ordered: tag === 'ol',
        items: [...node.querySelectorAll('li')].map((li) => li.innerHTML || li.textContent || ''),
      }));
      return;
    }

    if (tag === 'hr') {
      blocks.push(createBlock('separator'));
      return;
    }

    if (tag === 'figure') {
      const img = node.querySelector('img');
      blocks.push(createBlock('image', {
        url: img?.getAttribute('src') || '',
        alt: img?.getAttribute('alt') || '',
        caption: node.querySelector('figcaption')?.textContent?.trim() || '',
      }));
      return;
    }

    if (tag === 'img') {
      blocks.push(createBlock('image', {
        url: node.getAttribute('src') || '',
        alt: node.getAttribute('alt') || '',
        caption: '',
      }));
      return;
    }

    if (node.classList?.contains('page-columns')) {
      const columns = node.querySelectorAll(':scope > .page-column');
      blocks.push(createBlock('columns', {
        leftHtml: columns[0]?.innerHTML || '',
        rightHtml: columns[1]?.innerHTML || '',
      }));
      return;
    }

    blocks.push(createBlock('paragraph', { html: node.outerHTML || node.textContent || '' }));
  });

  return blocks.length ? blocks : [createBlock('paragraph', { html })];
}

export function blocksToHtml(blocks) {
  return (blocks || [])
    .map((block) => {
      switch (block.type) {
        case 'heading': {
          const level = Math.min(Math.max(Number(block.level) || 2, 1), 6);
          return `<h${level}>${escapeHtml(block.text || '')}</h${level}>`;
        }
        case 'paragraph':
          return block.html ? `<div class="page-paragraph">${block.html}</div>` : '';
        case 'quote':
          return block.html ? `<blockquote>${block.html}</blockquote>` : '';
        case 'list': {
          const tag = block.ordered ? 'ol' : 'ul';
          const items = (block.items || []).filter(Boolean).map((item) => `<li>${item}</li>`).join('');
          return items ? `<${tag}>${items}</${tag}>` : '';
        }
        case 'image':
          if (!block.url) return '';
          return `<figure class="page-image"><img src="${escapeAttr(block.url)}" alt="${escapeAttr(block.alt || '')}">${block.caption ? `<figcaption>${escapeHtml(block.caption)}</figcaption>` : ''}</figure>`;
        case 'separator':
          return '<hr class="page-separator">';
        case 'columns':
          return `<div class="page-columns"><div class="page-column">${block.leftHtml || ''}</div><div class="page-column">${block.rightHtml || ''}</div></div>`;
        default:
          return block.html || '';
      }
    })
    .filter(Boolean)
    .join('\n');
}

function escapeHtml(value) {
  return String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function escapeAttr(value) {
  return escapeHtml(value).replace(/'/g, '&#39;');
}

export const blockTypes = [
  { type: 'heading', label: 'Heading', icon: 'fas fa-heading' },
  { type: 'paragraph', label: 'Paragraph', icon: 'fas fa-paragraph' },
  { type: 'quote', label: 'Quote', icon: 'fas fa-quote-left' },
  { type: 'list', label: 'List', icon: 'fas fa-list-ul' },
  { type: 'image', label: 'Image', icon: 'fas fa-image' },
  { type: 'separator', label: 'Divider', icon: 'fas fa-minus' },
  { type: 'columns', label: 'Two Columns', icon: 'fas fa-columns' },
];

import { useBlockProps } from '@wordpress/block-editor';

function toEmbed(url) {
  if (!url) return '';
  try {
    const u = new URL(url);
    if (u.hostname.includes('google') && u.pathname.includes('/maps') && (u.pathname.includes('/embed') || u.searchParams.get('pb'))) return url;
    return 'https://www.google.com/maps?output=embed&q=' + encodeURIComponent(url);
  } catch(e) {
    return 'https://www.google.com/maps?output=embed&q=' + encodeURIComponent(url);
  }
}

export default function save({ attributes }) {
  const { mapsUrl } = attributes;
  const embed = toEmbed(mapsUrl);
  return (
    <div { ...useBlockProps.save({ className: 'cb-mapembed fade-up' }) }>
      { embed ? <iframe src={ embed } loading="lazy" referrerPolicy="no-referrer-when-downgrade" allowFullScreen title="Map"></iframe> : null }
    </div>
  );
}

import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
  const { blockId, title } = attributes;
  const panelId = `acc-panel-${blockId}`;
  return (
    <div {...useBlockProps.save({ className: 'cb-accordion' })}>
      <button
        type="button"
        className="cb-accordion__trigger"
        aria-expanded="false"
        aria-controls={panelId}
      >
        <svg className="cb-accordion__icon" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" strokeWidth="2" />
        </svg>
        <span className="cb-accordion__title">{title}</span>
      </button>
      <div id={panelId} className="cb-accordion__panel" hidden>
        <InnerBlocks.Content />
      </div>
    </div>
  );
}

import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { useState, useEffect } from '@wordpress/element';

const ALLOWED_BLOCKS = ['core/paragraph', 'core/heading', 'core/list', 'core/image', 'core/table'];

const TEMPLATE = [
  ['core/table', {
    body: [
      [{ content: 'Specification' }, { content: 'Value' }],
      [{ content: 'Nominal power' }, { content: '400 W' }],
      [{ content: 'Module efficiency' }, { content: '20%' }],
      [{ content: 'Operating temperature' }, { content: '-40\u00b0C to 85\u00b0C' }]
    ]
  }]
];

export default function Edit({ attributes, setAttributes, clientId }) {
  const { blockId } = attributes;
  useEffect(() => {
    if (!blockId) {
      setAttributes({ blockId: clientId });
    }
  }, [blockId, clientId]);

  const panelId = `ts-panel-${blockId || clientId}`;
  const [isOpen, setIsOpen] = useState(true);
  const toggle = () => setIsOpen(!isOpen);
  const blockProps = useBlockProps({ className: `cb-tech-spec${isOpen ? ' is-open' : ''}` });

  return (
    <div {...blockProps}>
      <button
        type="button"
        className="cb-tech-spec__trigger"
        onClick={toggle}
        aria-expanded={isOpen}
        aria-controls={panelId}
      >
        <span className="cb-tech-spec__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" strokeWidth="2" /></svg>
        </span>
        <span className="cb-tech-spec__title">Technical specification</span>
      </button>
      {isOpen && (
        <div id={panelId} className="cb-tech-spec__panel">
          <InnerBlocks allowedBlocks={ALLOWED_BLOCKS} template={TEMPLATE} templateLock={false} />
        </div>
      )}
    </div>
  );
}

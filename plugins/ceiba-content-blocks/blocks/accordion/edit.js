import { InnerBlocks, useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { TextControl } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';

const ALLOWED_BLOCKS = [
  'core/paragraph',
  'core/heading',
  'core/list',
  'core/image',
  'core/table'
];

export default function Edit({ attributes, setAttributes, clientId }) {
  const { blockId, title } = attributes;
  useEffect(() => {
    if (!blockId) {
      setAttributes({ blockId: clientId });
    }
  }, [blockId, clientId]);

  const panelId = `acc-panel-${blockId || clientId}`;
  const [isOpen, setIsOpen] = useState(true);
  const toggle = () => setIsOpen(!isOpen);
  const blockProps = useBlockProps({ className: `cb-accordion${isOpen ? ' is-open' : ''}` });

  return (
    <div {...blockProps}>
      <InspectorControls>
        <TextControl
          label="Title"
          value={title}
          onChange={(value) => setAttributes({ title: value })}
        />
      </InspectorControls>
      <button
        type="button"
        className="cb-accordion__trigger"
        onClick={toggle}
        aria-expanded={isOpen}
        aria-controls={panelId}
      >
        <span className="cb-accordion__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" strokeWidth="2" /></svg>
        </span>
        <span className="cb-accordion__title">{title}</span>
      </button>
      {isOpen && (
        <div id={panelId} className="cb-accordion__panel">
          <InnerBlocks allowedBlocks={ALLOWED_BLOCKS} templateLock={false} />
        </div>
      )}
    </div>
  );
}

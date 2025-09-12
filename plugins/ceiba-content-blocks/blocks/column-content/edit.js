import { __ } from '@wordpress/i18n';
import {
  useBlockProps,
  InspectorControls,
  InnerBlocks,
} from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl, Notice } from '@wordpress/components';
import { useMemo } from '@wordpress/element';

export default function Edit({ attributes, setAttributes }) {
  const { columnsCount = 3, layoutMode = 'contained' } = attributes;

  const hasBg = !!attributes?.backgroundColor || !!attributes?.style?.color?.background || !!attributes?.style?.color?.gradient;
  const forceFull = layoutMode === 'full';
  const alignSuffix = forceFull ? ' alignfull' : (attributes?.align ? ` align${attributes.align}` : (hasBg ? ' alignfull' : ''));

  const blockProps = useBlockProps({ className: `ceiba-ccb${alignSuffix} cols-${columnsCount}` });

  const columnsTemplate = useMemo(() => Array.from({ length: Math.min(Math.max(columnsCount, 1), 4) }, () => ['ceiba/column-content-item']), [columnsCount]);
  const template = useMemo(() => ([
    ['core/group', { className: 'ccb__top' }, [
      ['core/heading', { level: 2 }],
      ['core/paragraph']
    ]],
    ['core/group', { className: `ccb__columns` }, columnsTemplate],
    ['core/group', { className: 'ccb__bottom' }, [
      ['core/paragraph'],
      ['core/buttons']
    ]]
  ]), [columnsTemplate]);

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Layout', 'ceiba')} initialOpen>
          <SelectControl
            label={__('Layout mode', 'ceiba')}
            value={layoutMode}
            options={[
              { label: __('Contained', 'ceiba'), value: 'contained' },
              { label: __('Full width (background full-bleed, inner contained)', 'ceiba'), value: 'full' }
            ]}
            onChange={(v) => setAttributes({ layoutMode: v })}
            help={__('Full width makes the wrapper alignfull so background spans edge-to-edge.', 'ceiba')}
          />
          <RangeControl
            label={__('Number of columns', 'ceiba')}
            value={columnsCount}
            onChange={(v) => setAttributes({ columnsCount: Math.min(Math.max(v, 1), 4) })}
            min={1}
            max={4}
          />
          {columnsCount > 4 && (
            <Notice status="warning" isDismissible={false}>
              {__('Maximum 4 columns supported.', 'ceiba')}
            </Notice>
          )}
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <section className="ccb__inner" style={{ maxWidth: 'var(--wp--style--global--content-size)', width: '100%', marginLeft: 'auto', marginRight: 'auto' }}>
          <InnerBlocks
            template={template}
            templateLock={false}
            allowedBlocks={['core/group','core/heading','core/paragraph','core/buttons','ceiba/column-content-item','core/list','core/list-item','core/image']}
          />
        </section>
      </div>
    </>
  );
}


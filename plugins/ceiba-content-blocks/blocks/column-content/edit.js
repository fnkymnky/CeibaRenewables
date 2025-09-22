import { __ } from '@wordpress/i18n';
import {
  useBlockProps,
  InspectorControls,
  InnerBlocks,
} from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl, Notice } from '@wordpress/components';
import { useEffect, useMemo } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { createBlock } from '@wordpress/blocks';

export default function Edit({ attributes, setAttributes, clientId }) {
  const { columnsCount = 3, layoutMode = 'contained' } = attributes;

  const hasBg = !!attributes?.backgroundColor || !!attributes?.style?.color?.background || !!attributes?.style?.color?.gradient;
  const forceFull = layoutMode === 'full';
  const alignSuffix = forceFull ? ' alignfull' : (attributes?.align ? ` align${attributes.align}` : (hasBg ? ' alignfull' : ''));

  const blockProps = useBlockProps({ className: `ceiba-ccb${alignSuffix} cols-${columnsCount}` });

  // Keep template minimal to avoid insert failures if child scripts load late.
  const columnsTemplate = useMemo(() => [], []);
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

  // Locate the child Group that holds the columns and keep its children in sync with columnsCount
  const { columnsGroup, columnsChildren } = useSelect(
    (select) => {
      const be = select('core/block-editor');
      const children = be.getBlocks(clientId) || [];
      const group = children.find(
        (b) => b?.name === 'core/group' && (b?.attributes?.className || '').includes('ccb__columns')
      );
      return {
        columnsGroup: group,
        columnsChildren: group ? be.getBlocks(group.clientId) : [],
      };
    },
    [clientId]
  );

  const { insertBlocks, removeBlocks } = useDispatch('core/block-editor');

  useEffect(() => {
    if (!columnsGroup) return;
    const desired = Math.min(Math.max(columnsCount || 1, 1), 4);
    const current = (columnsChildren || []).length;

    if (current === desired) return;

    if (current < desired) {
      const toAdd = desired - current;
      const newBlocks = Array.from({ length: toAdd }, () => createBlock('ceiba/column-content-item'));
      // Insert at the end of the columns group
      insertBlocks(newBlocks, current, columnsGroup.clientId);
    } else if (current > desired) {
      const toRemove = columnsChildren.slice(desired).map((b) => b.clientId);
      if (toRemove.length) removeBlocks(toRemove);
    }
  }, [columnsGroup, columnsChildren?.length, columnsCount, insertBlocks, removeBlocks]);

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

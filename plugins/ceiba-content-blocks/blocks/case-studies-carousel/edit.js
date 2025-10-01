import { __ } from '@wordpress/i18n';
import { useMemo, Fragment } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, CheckboxControl, Button, Spinner, Notice } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
  const { includeIds = [] } = attributes;
  const blockProps = useBlockProps({ className: 'is-edit' });

  const { posts, isResolving } = useSelect(
    (select) => {
      const s = select(coreStore);
      const q = { per_page: 100, order: 'desc', orderby: 'date' };
      return {
        posts: s.getEntityRecords('postType', 'project', q),
        isResolving: s.isResolving('getEntityRecords', ['postType', 'project', q]),
      };
    },
    []
  );

  const items = useMemo(() => (posts || []).map(p => ({
    id: p.id,
    label: p.title?.rendered ? p.title.rendered.replace(/<[^>]+>/g, '') : `#${p.id}`,
  })), [posts]);

  const toggle = (id, checked) => {
    const set = new Set(includeIds);
    if (checked) {
      if (set.size >= 6) return;
      set.add(id);
    } else {
      if (set.size <= 1) return;
      set.delete(id);
    }
    setAttributes({ includeIds: Array.from(set).filter(Boolean) });
  };

  const move = (index, dir) => {
    const next = [...includeIds];
    const swap = index + dir;
    if (swap < 0 || swap >= next.length) return;
    [next[index], next[swap]] = [next[swap], next[index]];
    setAttributes({ includeIds: next });
  };

  const selected = includeIds
    .map(id => items.find(i => i.id === id))
    .filter(Boolean);

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={__('Projects', 'ceiba')} initialOpen>
          {isResolving && <Spinner />}
          {!isResolving && items.length === 0 && (
            <Notice status="info" isDismissible={false}>{__('No projects found.', 'ceiba')}</Notice>
          )}
          {!isResolving && items.map(item => (
            <CheckboxControl
              key={item.id}
              label={item.label}
              checked={includeIds.includes(item.id)}
              onChange={(v) => toggle(item.id, v)}
            />
          ))}
          <div style={{ marginTop: 12, fontSize: 12, color: '#666' }}>
            {includeIds.length} / 6 selected
          </div>
        </PanelBody>

        <PanelBody title={__('Order', 'ceiba')} initialOpen={false}>
          {selected.length ? selected.map((item, index) => (
            <div key={item.id} style={{ display: 'grid', gridTemplateColumns: '1fr auto', gap: 8, alignItems: 'center', marginBottom: 6 }}>
              <div style={{ overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{item.label}</div>
              <div style={{ display: 'inline-flex', gap: 6 }}>
                <Button icon="arrow-up-alt2" label={__('Up', 'ceiba')} onClick={() => move(index, -1)} disabled={index === 0} />
                <Button icon="arrow-down-alt2" label={__('Down', 'ceiba')} onClick={() => move(index, +1)} disabled={index === selected.length - 1} />
              </div>
            </div>
          )) : (
            <Notice status="warning" isDismissible={false}>{__('Select at least one item.', 'ceiba')}</Notice>
          )}
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div className="ceiba-cs-carousel__placeholder">
          <strong>{__('Projects - Carousel', 'ceiba')}</strong>
          <div className="hint">
            { includeIds.length ? __('Front-end will render selected items in this order.', 'ceiba') : __('Tick items in the sidebar (1–6).', 'ceiba') }
          </div>
        </div>
      </div>
    </Fragment>
  );
}

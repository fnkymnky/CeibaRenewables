import { __ } from '@wordpress/i18n';
import {
  useBlockProps,
  InspectorControls,
  RichText,
  MediaUpload,
  MediaUploadCheck,
} from '@wordpress/block-editor';
import {
  PanelBody,
  Button,
  TextControl,
} from '@wordpress/components';
import { useMemo } from '@wordpress/element';

const emptyMember = () => ({
  imageId: 0,
  imageUrl: '',
  imageAlt: '',
  name: '',
  title: '',
  bio: '',
});

export default function Edit({ attributes, setAttributes }) {
  const {
    title = '',
    intro = '',
    members = [],
  } = attributes;

  const blockProps = useBlockProps({ className: 'ceiba-team-grid is-edit' });
  const list = useMemo(() => (Array.isArray(members) ? members : []), [members]);

  const update = (idx, patch) => {
    const next = [...list];
    next[idx] = { ...next[idx], ...patch };
    setAttributes({ members: next });
  };

  const addMember = () => setAttributes({ members: [...list, emptyMember()] });
  const removeMember = (idx) => {
    const next = list.filter((_, i) => i !== idx);
    setAttributes({ members: next });
  };
  const move = (idx, dir) => {
    const swap = idx + dir;
    if (swap < 0 || swap >= list.length) return;
    const next = [...list];
    [next[idx], next[swap]] = [next[swap], next[idx]];
    setAttributes({ members: next });
  };

  const setImage = (idx, media) => {
    if (!media) return update(idx, { imageId: 0, imageUrl: '', imageAlt: '' });
    update(idx, { imageId: media.id, imageUrl: media.url, imageAlt: media.alt || media.title || '' });
  };

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Section', 'ceiba')} initialOpen>
          <TextControl
            label={__('Section title (H2)', 'ceiba')}
            value={title}
            onChange={(v) => setAttributes({ title: v })}
          />
          <TextControl
            label={__('Intro (short, optional)', 'ceiba')}
            value={intro}
            onChange={(v) => setAttributes({ intro: v })}
          />
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div className="ceiba-team-grid__head">
          <RichText
            tagName="h2"
            placeholder={__('Add a title…', 'ceiba')}
            value={title}
            allowedFormats={[]}
            onChange={(v) => setAttributes({ title: v })}
            className="ceiba-team-grid__title"
          />
          <RichText
            tagName="div"
            placeholder={__('Intro text…', 'ceiba')}
            value={intro}
            onChange={(v) => setAttributes({ intro: v })}
            className="ceiba-team-grid__intro"
          />
        </div>

        <div className="ceiba-team-grid__controls">
          <Button variant="primary" onClick={addMember}>
            {__('Add member', 'ceiba')}
          </Button>
        </div>

        <div className="ceiba-team-grid__list">
          {list.length === 0 && (
            <div className="ceiba-team-grid__empty">{__('No team members yet. Add one.', 'ceiba')}</div>
          )}

          {list.map((m, idx) => (
            <div key={idx} className="ceiba-team-grid__row">
              <div className="ceiba-team-grid__row-head">
                <strong>{m.name || __('Team member', 'ceiba')}</strong>
                <div className="row-actions">
                  <Button onClick={() => move(idx, -1)} disabled={idx === 0}>↑</Button>
                  <Button onClick={() => move(idx, +1)} disabled={idx === list.length - 1}>↓</Button>
                  <Button isDestructive onClick={() => removeMember(idx)}>{__('Remove', 'ceiba')}</Button>
                </div>
              </div>

              <div className="ceiba-team-grid__row-grid">
                <div className="field">
                  <MediaUploadCheck>
                    <MediaUpload
                      onSelect={(media) => setImage(idx, media)}
                      allowedTypes={['image']}
                      value={m.imageId}
                      render={({ open }) => (
                        <Button variant="secondary" onClick={open}>
                          {m.imageId ? __('Replace headshot', 'ceiba') : __('Select headshot', 'ceiba')}
                        </Button>
                      )}
                    />
                  </MediaUploadCheck>
                  {m.imageUrl ? (
                    <div className="headshot-preview">
                      <img src={m.imageUrl} alt="" />
                    </div>
                  ) : (
                    <div className="headshot-placeholder" />
                  )}
                  <TextControl
                    label={__('Alt text', 'ceiba')}
                    value={m.imageAlt || ''}
                    onChange={(v) => update(idx, { imageAlt: v })}
                  />
                </div>

                <div className="field">
                  <TextControl
                    label={__('Name (H3)', 'ceiba')}
                    value={m.name}
                    onChange={(v) => update(idx, { name: v })}
                  />
                  <TextControl
                    label={__('Job title (H4)', 'ceiba')}
                    value={m.title}
                    onChange={(v) => update(idx, { title: v })}
                  />
                  <TextControl
                    label={__('Short bio (shown on “Read bio”)', 'ceiba')}
                    value={m.bio}
                    onChange={(v) => update(idx, { bio: v })}
                  />
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </>
  );
}

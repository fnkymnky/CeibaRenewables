import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, BlockControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, Notice, ToolbarButton } from '@wordpress/components';
import { useMemo, useState, useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { store as blockEditorStore } from '@wordpress/block-editor';

function toEmbed(url) {
  if (!url) return '';
  try {
    const u = new URL(url);
    if (u.hostname.includes('google') && u.pathname.includes('/maps') && (u.pathname.includes('/embed') || u.searchParams.get('pb'))) return url;
    const m = u.href.match(/@(-?\d+(\.\d+)?),(-?\d+(\.\d+)?),(\d+(\.\d+)?)z/);
    if (m) return `https://www.google.com/maps?output=embed&ll=${m[1]},${m[3]}&z=${Math.round(parseFloat(m[5]))}`;
    if (u.searchParams.get('q')) return `https://www.google.com/maps?output=embed&q=${encodeURIComponent(u.searchParams.get('q'))}`;
    return `https://www.google.com/maps?output=embed&q=${encodeURIComponent(url)}`;
  } catch(e) {
    return `https://www.google.com/maps?output=embed&q=${encodeURIComponent(url)}`;
  }
}

export default function Edit({ attributes, setAttributes, clientId }) {
  const { mapsUrl } = attributes;
  const embed = useMemo(() => toEmbed(mapsUrl), [mapsUrl]);
  const isShort = useMemo(() => {
    try { return new URL(mapsUrl).hostname.includes('maps.app.goo.gl'); } catch { return false; }
  }, [mapsUrl]);

  const isSelected = useSelect((select) => select(blockEditorStore).isBlockSelected(clientId), [clientId]);
  const [interact, setInteract] = useState(false);
  useEffect(() => { if (!isSelected && interact) setInteract(false); }, [isSelected, interact]);

  const blockProps = useBlockProps({ className: 'cb-mapembed' });

  return (
    <>
      <BlockControls>
        <ToolbarButton
          icon="move"
          isPressed={interact}
          onClick={() => setInteract((v) => !v)}
          label={ interact ? __('Disable Map', 'ceiba') : __('Enable Map', 'ceiba') }
          text={ interact ? __('Map On', 'ceiba') : __('Map Off', 'ceiba') }
        />
      </BlockControls>

      <InspectorControls>
        <PanelBody title={ __('Map', 'ceiba') } initialOpen>
          <TextControl
            label={ __('Google Maps Share URL', 'ceiba') }
            value={ mapsUrl }
            onChange={(v) => setAttributes({ mapsUrl: v })}
            placeholder="https://maps.app.goo.gl/..."
          />
          {isShort && (
            <Notice status="info" isDismissible={ false }>
              { __('Short links embed. For exact focus paste the “Embed a map” URL (…/maps/embed?pb=…).', 'ceiba') }
            </Notice>
          )}
        </PanelBody>
      </InspectorControls>

      <div { ...blockProps }>
        { embed ? (
          <>
            <iframe
              src={ embed }
              loading="lazy"
              referrerPolicy="no-referrer-when-downgrade"
              allowFullScreen
              title="Map"
              style={{ pointerEvents: interact ? 'auto' : 'none' }}
            />
            { !interact && <div className="cb-mapembed__overlay">{ __('Map preview locked • Use toolbar to enable', 'ceiba') }</div> }
          </>
        ) : (
          <div className="cb-mapembed__placeholder">{ __('Add a Google Maps Share URL in the sidebar', 'ceiba') }</div>
        ) }
      </div>
    </>
  );
}

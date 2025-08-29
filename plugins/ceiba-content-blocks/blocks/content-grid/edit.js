import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import { PanelBody, RangeControl, __experimentalUnitControl as UnitControl } from '@wordpress/components';

const TEMPLATE = [ [ 'ceiba/content-grid-item' ] ];

export default function Edit({ attributes, setAttributes }) {
	const { columns, gap } = attributes;

	const blockProps = useBlockProps({
		className: `content-grid columns-${ columns }`,
		style: {
			'--content-grid-gap': gap,
			'--content-grid-columns': String(columns) // unitless for repeat()
		}
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __('Grid settings', 'ceiba') } initialOpen>
					<RangeControl
						label={ __('Columns', 'ceiba') }
						value={ columns }
						onChange={ (v) => setAttributes({ columns: v }) }
						min={ 1 }
						max={ 6 }
						allowReset={ false }
					/>
					<UnitControl
						label={ __('Gap', 'ceiba') }
						value={ gap }
						onChange={ (v) => setAttributes({ gap: v || '0' }) }
						units={[
							{ value: 'px', label: 'px' },
							{ value: 'rem', label: 'rem' },
							{ value: 'em',  label: 'em' }
						]}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<InnerBlocks
					allowedBlocks={[ 'ceiba/content-grid-item' ]}
					
					renderAppender={ InnerBlocks.ButtonBlockAppender }
				/>
			</div>
		</>
	);
}

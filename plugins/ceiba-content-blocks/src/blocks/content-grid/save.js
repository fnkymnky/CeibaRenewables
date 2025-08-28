import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const { columns, gap } = attributes;

	const blockProps = useBlockProps.save({
		className: `ceiba-content-grid columns-${ columns }`,
		style: { '--content-grid-gap': gap, '--content-grid-columns': String(columns) },
	});

	return (
		<div {...blockProps}>
			<InnerBlocks.Content />
		</div>
	);
}

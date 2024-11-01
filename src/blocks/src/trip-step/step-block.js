import '../editor.scss';
import '../style.scss';

const {__} = wp.i18n; // Import __() from wp.i18n
const {registerBlockType} = wp.blocks; // Import registerBlockType() from wp.blocks
const {
	Fragment
} = wp.element;

import {
	Spinner,
	SelectControl,
} from '@wordpress/components';

registerBlockType('traveledmap/embedded-trip-step-block', {
	title: __('Step scroll anchor'), // Block title.
	icon: 'location',
	category: 'traveledmap',
	keywords: [
		__('Embedded Trip Step'),
		__('Embed'),
		__('Step'),
		__('Trip'),
		__('City'),
		__('Place'),
		__('Scroll'),
		__('Anchor'),
	],
	attributes: {
		tripStepsJson: {
			type: 'string',
			source: 'meta',
			meta: 'traveledmap_trip_steps',
		},
		tripSteps: {
			type: 'string',
		},
		location: {
			type: 'string',
		},
	},
	edit: function (props) {
		const {
			attributes: {location, tripStepsJson, tripSteps}, setAttributes
		} = props;

		const tryToGetStepsObject = (iteration = 0) => {
			const methods = ['doubleStringify', 'simpleStringify']

			if (iteration > methods.length) {
				return null;
			}

			const method = methods[iteration]
			try {
				switch (method) {
					case 'doubleStringify':
						return JSON.parse(JSON.parse(tripStepsJson));
					case 'simpleStringify':
						return JSON.parse(tripStepsJson[0]);
				}
			} catch (e) {
				console.debug('Method for getting steps from metadata failed:', method, e)
				return tryToGetStepsObject(iteration + 1);
			}
		}

		if (!tripSteps && tripStepsJson) {
			console.debug({ tripStepsJson });
			try {
				const tripSteps = tryToGetStepsObject();
				console.debug({ tripSteps })
				if (tripSteps) {
					setAttributes({
						tripSteps: tripSteps,
						location: location && location.length > 0 ? location : Object.keys(tripSteps)[0]
					});
				} else {
					console.warn('Trip steps was not defined', tripSteps, tripStepsJson)
				}
			} catch (e) {
				console.error('An error occurred while setting trip steps in step-block', e)
			}
		}

		return !tripSteps ? (
			<div className="flex-center">
				<Spinner/>
			</div>
		) : (
			<SelectControl
				label="Choose the step the map should move on when reader reaches this section of the post"
				value={location}
				options={Object.keys(tripSteps).map((hash) => ({
					label: tripSteps[hash],
					value: hash,
				}))}
				onChange={(newLocation) => setAttributes({location: newLocation})}
			/>
		);
	},

	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	save: function (props) {
		const { attributes: { location } } = props;
		return (
			<span className="traveledmap-trip-step" data-step={location}></span>
		);
	},
});

const {registerBlockType} = wp.blocks
const { __ } = wp.i18n

const options = {
	title: __( 'Distance Calculator', 'distance-calc' ),
	description : __( 'Add a distance price Calculator', 'distance-calc' ),
	category: 'widgets',
	icon : 'location-alt',
	attributes : {
		costPerMile : {
			type :'string',
			source : 'attribute',
			selector:'iframe',
			attribute : 'data-costpermile',
		},
		iframeUrl : {
			type :'string',
			source : 'attribute',
			selector:'iframe',
			attribute : 'data-src',
		},
	},
	supports: {
		html: false,
		customClassName : false,
		alignWide:false,
	},
	
	edit({attributes,classname,setAttributes,isSelected}){
		let defaultCostPerMile = window.dcData && window.dcData.costPerMile ? parseFloat(window.dcData.costPerMile,10) : 2.5
		let iframeUrl = window.dcData && window.dcData.iframeUrl ? window.dcData.iframeUrl : 'https://swimmania.life/gmaps/taxi/index.php'
		let costPerMile = attributes.costPerMile ? attributes.costPerMile : defaultCostPerMile

		if( iframeUrl!=attributes.iframeUrl ){
			setAttributes(Object.assign({},attributes,{ 
				iframeUrl 
			}))
		}


		let handleInputChange = ( event )=>{
			costPerMile = event.target.value;
			setAttributes(Object.assign({},attributes,{ 
				costPerMile 
			}));
		}
		
		return (
			<div class="wp-block-distance-calc-distance">
				<h5>Distance cost Calculator</h5>
				<label for="distance-calc">Edit the Cost per Mile</label>
				&nbsp; $ 
				<input id="distance-calc" type="text" name="cost-per-mile" value={costPerMile} onChange={handleInputChange}/>
			</div>
		)
	},

	save({ attributes }){
		let defaultCostPerMile = window.dcData && window.dcData.costPerMile ? parseFloat(window.dcData.costPerMile,10) : 2.5
		let iframeUrl = window.dcData && window.dcData.iframeUrl ? window.dcData.iframeUrl : 'https://swimmania.life/gmaps/taxi/index.php'
		let costPerMile = attributes.costPerMile ? attributes.costPerMile : defaultCostPerMile
		iframeUrl = attributes.iframeUrl ? attributes.iframeUrl : iframeUrl

		return (
			<div>
				<iframe src={iframeUrl+'?cost_per_mile='+costPerMile} data-costpermile={costPerMile} data-src={iframeUrl} style="border:none !important;width:100%;height:300px"></iframe>
			</div>
		)
	}
}

registerBlockType( 'distance-calc/distance', options );

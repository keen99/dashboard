stuff that should happen


make graphtemplate metrics it's own array to merge each thing associated to a metric together



fix the ability to define a specific color for each metric

add some view options to the template - which sum/agg/host/service combos allowed


expand/collapse service and host view someway  (to only show the selected service/host vs just taking us to them)

//...if you use a named color that isnt in the list, we should alert


..alias/label when no alias speced needs help








new metrics structure


graphTemplate[<name>] [
	....
	metric [#?] [
		pattern = (system)
		alias = system
		color = 		#for specific color vs random list, or just use extras?
		function =		#these wrap the metric?
		axis = left/right #place on this axis for multi-axis

	colors [ random list ]
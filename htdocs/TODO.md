stuff that should happen



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
		function =		#these wrap the metric? secondYAxis for the other axis

	colors [ random list ]





UI button to update cache (and therefor a flag, and therefor a cron..)


if we have a found metric due to suffixpattern, but we dont have a metric[] entry, we create a broken graph.  (particularly a problem w/ individual graphs, because that doesnt seem to care and creates a broken graph) see //dsr match TODO for where it starts...


when agg is selected, we lose our host/section list.

w/ service+agg, our alias is wrong.  should show our pattern.  also, our scale is wrong, so we're not agg/stacking right.


agg at all - stack isnt' stacking right. counts are off.


sum+host = random colors (gotta fix it down further), aliases broken

sum really only works with metrics with multiple matches (it sums them), not with fixed metrics..


colors - metric specific colors dont work with names.  need magic.  more limited names, and bad err handling (because we dont hit graphfactory)

areaAlpha support to graphfactory..
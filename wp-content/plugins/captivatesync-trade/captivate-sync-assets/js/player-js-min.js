jQuery(document).ready(function(t){var e=new CP("iframe");t(".cp-timestamp").click(function(){var n=t(this).data("timestamp");e.seekTo(function(t){for(var e=t.split(":"),n=0,a=1;e.length>0;)n+=a*parseInt(e.pop(),10),a*=60;return n}(n))})});
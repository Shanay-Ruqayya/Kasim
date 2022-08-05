jQuery(document).ready(function(e){e("body").tooltip({selector:".cfmsync-tooltip"});var s=new ClipboardJS(".clipboard");s.on("success",function(s){e(s.trigger).addClass("fade").tooltip("show"),s.clearSelection()}),s.on("error",function(s){var o=e(s.trigger).data("clipboard-text");e(s.trigger).attr("title",o).tooltip("fixTitle").addClass("fadeError").tooltip("show")}),e(".cb-tooltip").tooltip({placement:"top",trigger:"manual",title:"Copied!"}).tooltip("hide"),e(".cb-tooltip").on("shown.bs.tooltip",function(){var s=4294967295;e(".cb-tooltip.fade").length&&(s=2e3),e(".cb-tooltip.fadeError").length&&(s=1e4);var o=e(this),t=o[0];null==t.myShowTooltipEventNum?t.myShowTooltipEventNum=0:t.myShowTooltipEventNum++;var n=t.myShowTooltipEventNum;setTimeout(function(){t.myShowTooltipEventNum==n&&(o.tooltip("hide"),o.removeClass("fade"))},s)}),e(document).on("click","button[name=syncShows]",function(s){s.preventDefault(),e.ajax({url:cfmsync.ajaxurl,type:"post",data:{action:"sync-shows",_nonce:cfmsync.ajaxnonce},beforeSend:function(s){e("button[name=syncShows]").prop("disabled",!0),e("#cfm-message").html("<p>Syncing shows and episodes...</p>").fadeIn()},success:function(s){"success"==s?e("#cfm-message").html("<p>Sync complete!</p>"):e("#cfm-message").html("<p>"+s+"</p>"),location.reload(!0)}}),s.preventDefault()}),e(document).on("click","button[name=CFMPickShows]",function(s){s.preventDefault(),e.ajax({url:cfmsync.ajaxurl,type:"post",data:{action:"get-shows",_nonce:cfmsync.ajaxnonce},success:function(s){if("null"!=s){var o=JSON.parse(s),t="";if(o.length>=1)for(var n=0;n<o.length;++n){var a=o[n].enabled?"checked":"";t+="<li class='cfm_show_selectors cfm_show_"+o[n].id+"'><input type='checkbox' "+a+" id='cfm_show_"+o[n].id+"' value='"+o[n].id+"' name='showsToSync'> <label for='cfm_show_"+o[n].id+"'>"+o[n].title+"</label><div class='cfm_error-status'></div></li>",n==o.length-1&&(e(".cfm-sync-shows").html(t),e("#SyncShows").modal("show"))}else e(".cfm-sync-add-show").show()}else e(".select-shows").hide(),e(".cfm-sync-shows").hide(),e(".cfm-sync-add-show").show(),e("#SyncShows").modal("show")}}),s.preventDefault()}),e(document).on("click","button[name=selectShows]",function(s){s.preventDefault();let o=[];e.each(e("input[name='showsToSync']:checked"),function(){o.push(e(this).val())}),e.ajax({url:cfmsync.ajaxurl,type:"post",data:{action:"select-shows",shows:o,_nonce:cfmsync.ajaxnonce},beforeSend:function(s){e("button[name=selectShows]").prop("disabled",!0),e(".cfm_show_selectors input").prop("disabled",!0),e("#SyncShows .fa-spinner.hide").removeClass("hide"),e(".cfm-sync-progress").html("<p>Syncing shows and episodes...</p>").fadeIn()},success:function(s){var o=JSON.parse(s);if(o.return){var t=o.return.length;e(".cfm_show_selectors input").attr("disabled","disabled");for(var n=0;n<o.return.length;++n)0==o.return[n].success?(e(".cfm_show_"+o.return[n].id).addClass("cfm-failed"),e(".cfm_show_"+o.return[n].id+" .cfm_error-status").html(o.return[n].error)):t-=1;e("#SyncShows .fa-spinner").addClass("hide"),0==t?e(".cfm-sync-progress").html("<p>Shows and episodes synced successfully.</p>"):e(".cfm-sync-progress").html("<p>It looks like we've ran into a few issues whilst selecting these shows to sync.</p>")}else e(".cfm-sync-progress").html("<p>Shows already selected successfully.</p>"),e("#SyncShows .fa-spinner").addClass("hide");location.reload(!0)}}),s.preventDefault()}),e(document).on("change","select[name=page_for_show]",function(s){s.preventDefault();var o=e(this).prop("id").split("_")[1],t=e(this).val();e(document).disableFields("input[name=display_episodes]"),e(document).disableFields("select[name=page_for_show]"),e(document).disableFields("select[name=author_for_show]"),e.ajax({url:cfmsync.ajaxurl,type:"post",data:{action:"set-show-page",_nonce:cfmsync.ajaxnonce,show_id:o,page_id:t},success:function(s){"success"==s?cfmsync_toaster("success","Podcast episodes will appear on this page, now"):cfmsync_toaster("error",s),setTimeout(function(){e(document).enableFields("input[name=display_episodes]"),e(document).enableFields("select[name=page_for_show]"),e(document).enableFields("select[name=author_for_show]")},5e3)}}),s.preventDefault()}),e(document).on("change","select[name=author_for_show]",function(s){s.preventDefault();var o=e(this).prop("id").split("_")[1],t=e(this).val();e(document).disableFields("input[name=display_episodes]"),e(document).disableFields("select[name=page_for_show]"),e(document).disableFields("select[name=author_for_show]"),e.ajax({url:cfmsync.ajaxurl,type:"post",data:{action:"set-show-author",_nonce:cfmsync.ajaxnonce,show_id:o,author_id:t},success:function(s){"success"==s?cfmsync_toaster("success","Show author has been set successfully"):cfmsync_toaster("error",s),setTimeout(function(){e(document).enableFields("input[name=display_episodes]"),e(document).enableFields("select[name=page_for_show]"),e(document).enableFields("select[name=author_for_show]")},5e3)}}),s.preventDefault()}),e(document).on("change","input[name=display_episodes]",function(s){s.preventDefault();var o=e(this).prop("id").split("_")[1],t=this.checked?"1":"0";e(document).disableFields("input[name=display_episodes]"),e(document).disableFields("select[name=page_for_show]"),e(document).disableFields("select[name=author_for_show]"),e.ajax({url:cfmsync.ajaxurl,type:"post",data:{action:"set-display-episodes",_nonce:cfmsync.ajaxnonce,show_id:o,display_episodes:t},success:function(s){"success"==s?"0"==t?cfmsync_toaster("success","Episodes will not appear on the selected page"):cfmsync_toaster("success","Episodes will now appear on the selected page"):cfmsync_toaster("error",s),setTimeout(function(){e(document).enableFields("input[name=display_episodes]"),e(document).enableFields("select[name=page_for_show]"),e(document).enableFields("select[name=author_for_show]")},5e3)}}),s.preventDefault()}),e(document).on("click","#cfm-datatable-episodes a.cfm-trash-episode",function(s){s.preventDefault();var o=e(this).data("post-id"),t=e(this).data("nonce"),n=e(this).parent().parent();confirm("Are you sure you want to delete this episode? This episode will be removed from your Captivate account too.")&&e.ajax({url:cfmsync.ajaxurl,type:"post",data:{action:"trash-episode",_nonce:t,post_id:o},beforeSend:function(e){n.css({"background-color":"#ff3333"},500)},success:function(e){"success"==e?n.fadeOut(500,function(){n.remove()}):"captivate_error"==e?(n.fadeOut(500,function(){n.remove()}),alert("Episode moved to trash on Podcast Websites. It is not deleted on Captivate or do not exists.")):(n.css({"background-color":"#ffffff"}),alert("Something went wrong. Please contact support."))}}),s.preventDefault()}),e(document).on("click","button[name=removeCredentials]",function(s){s.preventDefault(),confirm("Are you sure you want to remove authentication on this website? User credentials, shows, and episodes will be removed from this site.")&&e.ajax({url:cfmsync.ajaxurl,type:"post",data:{action:"remove-credentials",_nonce:cfmsync.ajaxnonce},beforeSend:function(s){e("#cfm-message").html("<p>Removing user credentials, shows, and episodes...</p>").fadeIn()},success:function(s){"success"==s?(e("#cfm-message").html("<p>User credentials credentials, shows, and episodes removed successfully.</p>"),e(".cfm-content-wrap").hide()):e("#cfm-message").html("<p>"+s+"</p>")}}),s.preventDefault()}),e(document).on("click",".cfm-show-wrap .cfm-clear-publish-data",function(s){s.preventDefault();var o=e(this),t=o.closest(".cfm-show-wrap").prop("id").split("_")[1];if(confirm("Are you sure you want to clear the publish episode auto-save data on this show? All fields on publish episode screen for this show will be emptied.")){var n=t+"_cfm-form-publish-episode_save_storage";localStorage.removeItem(n),localStorage.removeItem(t+"_featured_image_url_local"),localStorage.removeItem(t+"_post_content_wp_local"),localStorage.removeItem(t+"_shownotes_local"),localStorage.removeItem(t+"_shownotes_local_html"),cfmsync_toaster("success","Publish episode data cleared successfully."),o.blur()}s.preventDefault()}),e.fn.disableFields=function(s){""!=s&&e(s).each(function(){e(this).prop("disabled",!0)})},e.fn.enableFields=function(s){""!=s&&e(s).each(function(){e(this).prop("disabled",!1)})}});
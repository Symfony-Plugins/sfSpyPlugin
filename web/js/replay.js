var progress     = 0;
var pause        = false;
var direction    = 1;

function showProgress()
{
  // Advance progress bar
  jQuery('#progress').css('width', ((progress) / duration * 700) + 'px');
  
  // Execute timeline events
  if(timeline[progress] != undefined)
  {
    for(var type in timeline[progress])
    {
      timeline_event = timeline[progress][type];
      if(type == page_type)
      {
        jQuery('#page').attr('src', page_url.replace('PLACEHOLDER', progress + start_date));
        jQuery('#latest_url').html(timeline_event);
      }
      else if(type == reload_type)
      {
        jQuery('#page').attr('src', page_url.replace('PLACEHOLDER', timeline_event[0]));
        jQuery('#latest_url').html(timeline_event[1]);
      }
      else if(type == post_type)
      {
        jQuery('#post_url').html(timeline_event[0]);
        jQuery('#post_vars').html(timeline_event[1]);
        jQuery('#post_data').toggle();
        setTimeout(hidePostData, post_visibility * 1000);
      }
    }
  }
  
  // Continue progress
  if(!pause && progress < duration && progress >= 0)
  {
    continueReplay();
  }
  else if(!pause && progress == duration)
  {
    pause = true;
  }
}

function continueReplay()
{
  progress += direction;
  setTimeout(showProgress, 1000 / replay_speed);
}

function togglePause()
{
  if(pause)
  {
    continueReplay();
  }
  pause = !pause;
}

function forward()
{
  if(replay_speed == 1 && direction == -1)
  {
    if(pause)
    {
      direction = 1;
    }
    togglePause();
  }
  else
  {
    replay_speed -= -1 * direction;
  }
}

function backward()
{
  if(replay_speed == 1 && direction == 1)
  {
    if(pause)
    {
      direction = -1;
    }
    togglePause();
  }
  else
  {
    replay_speed += -1 * direction;
  }
}

function rewind(new_progress)
{
  progress = new_progress ? new_progress : 0;
  if(pause)
  {
    togglePause();
  }
}

function hidePostData()
{
  jQuery('#post_data').hide();
}
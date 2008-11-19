heartbeat = {

  options: 
  {
    delay:  10000
  },

  beatfunction: function()
  {
  },

  timeoutobj: 
  {
    id: -1
  },

  set: function(options, onbeatfunction)
  {
    if (this.timeoutobj.id > -1)
    {
      clearTimeout(this.timeoutobj);
    }
    if (options)
    {
      jQuery.extend(this.options, options);
    }
    if (onbeatfunction)
    {
      this.beatfunction = onbeatfunction;
    }
    this.timeoutobj.id = setTimeout("heartbeat.beat();", this.options.delay);
  },

  beat: function()
  {
    if(this.options.url != undefined)
    {
      var target = this.options.url + "?timestamp=" + timestamp;
      if(this.options.target != undefined)
      {
        jQuery(this.options.target).load(target);
      }
      else
      {
        jQuery.getScript(target);
      }
    }
    this.timeoutobj.id = setTimeout("heartbeat.beat();", this.options.delay);
    this.beatfunction();
  }
};

<div id="filemanager">
<section class='container'>

  <section  class='row'>

  <div class='col-md-4'>
    <h2>File manager</h2>
  </div>

  <div class='col-md-8 text-right' style='padding-top:15px;'>
    <a class='btn btn-primary' data-toggle='modal' data-backdrop='static' data-target='.upload_file_to_storage'><i class="fa fa-upload" aria-hidden="true"></i> Upload</a>
    <a class='btn btn-primary' data-toggle='modal' data-backdrop='static' data-target='.new_folder'><i class="fa fa-folder" aria-hidden="true"></i> Create Folder</a>
  </div>

  </section>

<div class='panel panel-default col-md-2' style='padding:0px;'>
 <ul class="list-group">
  <a href='admin/file-manager?path=images'><li class="list-group-item">images</li></a>
  <li class="list-group-item">uploads</li>
  <li class="list-group-item">themes</li>
  <li class="list-group-item">plugins</li>
</ul>
</div>


<div class="panel panel-default col-md-10" >
  <div class="panel-body">
      <ol class="breadcrumb text-left">
			  <li><a href="admin/file-manager/index?mode={{$mode}}&path="><?= basename(storage_path()); ?></a></li>
        @foreach(explode("/",$current_dir) as $dir)
          <li><a href="admin/file-manager/index?mode={{$mode}}&path={{$dir}}">{{$dir}}</a></li>
        @endforeach
        <hr>
			</ol>

      <div id="workspace">
            @foreach($dirs as $dir)
                <div class='folder col-md-2' id="{{$dir}}" ondblclick="filemanager.dirOpen('admin/file-manager/index?mode={{$mode}}&path=<?= $old_path.$dir ?>');" >
                  
                  <div class="file-nav text-right">
                    <a data-toggle='modal' data-target=.delete_{{$dir}} data-file="{{$old_path.$dir}}" ><i class="fa fa-trash pull-right"></i></a>
                  </div>

                  {!! Html::img('resources/images/icons/dir.png') !!}
                  <b>{{$dir}}</b>
                </div>

              <?php 

                 Bootstrap::delete_confirmation(
                  "delete_".$dir,
                  trans('actions.are_you_sure'),
                  "<div style='color:black;'><b>".trans('actions.delete_this',['content_type'=>'dir']).": </b>".$dir." <b>?</b></div>",
                  "<a href='admin/file-manager/delete?file=storage/".$old_path.$dir."' type='button' class='btn btn-danger' onclick=\"filemanager.delete(event,'storage/".$old_path.$dir."')\" ><span class='glyphicon glyphicon-trash' aria-hidden='true'></span> ".trans('actions.delete')."</a>
                  <button type='button' class='btn btn-default' data-dismiss='modal'>".trans('actions.cancel')."</button>"
                  );

              ?>

            @endforeach

            @foreach($files as $file)
                <?php $file_parts = pathinfo($file) ?>
                <div class='file col-md-2' id="{{str_replace('.'.$file_parts['extension'],'',$file)}}" @if($mode=='embed') onclick='filemanager.returnFileUrl("<?= 'storage/'.$old_path.$file ?>");' @else data-toggle='modal' data-target='.{{$file}}-modal-xl' @endif >
                <div class="file-nav text-right">
                  <a href="admin/file-manager/download?file=storage/{{$old_path.$file}}"><i class="fa fa-download"></i></a>&nbsp
                  <a data-toggle='modal' data-target=".delete_{{str_replace('.'.$file_parts['extension'],'',$file)}}" ><i class="fa fa-trash"></i></a>
                </div>
                @if(isset($file_parts['extension']) && in_array($file_parts['extension'],$allowed_extensions['image']))
                  <img src="{{'storage/'.$old_path.$file}}" />
                @else
                 <img src="resources/images/icons/file.png" style='margin-bottom:5px;' />
                @endif
                  <b>{{$file}}</b>
                </div>

              <?php 

                 Bootstrap::delete_confirmation(
                  "delete_".str_replace('.'.$file_parts['extension'],'',$file),
                  trans('actions.are_you_sure'),
                  "<div style='color:black;'><b>".trans('actions.delete_this',['content_type'=>'file']).": </b>".$file." <b>?</b></div>",
                  "<a href='admin/file-manager/delete?file=storage/".$old_path.$file."' type='button' class='btn btn-danger' onclick=\"filemanager.delete(event,'storage/".$old_path.$file."')\" ><span class='glyphicon glyphicon-trash' aria-hidden='true'></span> ".trans('actions.delete')."</a>
                  <button type='button' class='btn btn-default' data-dismiss='modal'>".trans('actions.cancel')."</button>"
                  );

              ?>


            @endforeach
        </div>

  </div>
</div>

</section>


<style>

	.list-group-item:hover{
		border-radius: 0px;
		color:white;
		background-color: #337ab7;	
	}

  .file, .folder{
    overflow:hidden;
    height:160px;
    cursor:pointer;
    padding:5px;
    text-align:center;
  }

  .file a i, .folder a i{
    visibility:hidden;
  }

  .file:hover a i, .folder:hover a i{
    visibility:visible;
  }

	.file:hover, .folder:hover{
		border-radius:1.5px;
		color:white;
		background-color: #337ab7;
	}

  .folder img{
    width:75%;
    margin-top:10px;
  }

  .file-nav{
    margin-bottom: 5px;
  }

  .file img{
    object-fit:cover;
    width:100%;
    height:100px;
  }

</style>



<div class='modal upload_file_to_storage' id='upload_file_to_storage' tabindex='-1' role='dialog' aria-labelledby='upload_file_to_storage' aria-hidden='true'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header modal-header-primary'>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
        <h4 class='modal-title'>Upload file</h4>
      </div>
      <div class='modal-body'>

      <form action='admin/file-manager/fileupload' method='POST' enctype='multipart/form-data' onsubmit="filemanager.upload(event);">
      {{ csrf_field() }}
      <div class='form-group'>
        <label for='file'>Upload file:</label>
        <input type='hidden' name='dir_path' value="{{ $current_dir }}">
        <input name='up_file[]' id='input-2' type='file' class='file' multiple='true' data-show-upload='false' data-show-caption='true' required>
      </div>


      </div>
      <div class='modal-footer'>
        <button type='submit' class='btn btn-primary'>Upload</button>
        <button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div class='modal new_folder' id='new_folder' tabindex='-1' role='dialog' aria-labelledby='new_folder' aria-hidden='true'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header modal-header-primary'>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
        <h4 class='modal-title'>Create new folder</h4>
      </div>
      <div class='modal-body'>

  <form action='admin/file-manager/new-folder' method='POST' enctype='multipart/form-data' onsubmit="filemanager.newFolder(event);">
      {{ csrf_field() }}
    <div class='form-group'>
      <input type='hidden' name='dir_path' value="{{ $old_path }}">
      <div class='form-group' >
       <label for='title'>Name:</label>  
        <input type='text' class='form-control' name='new_folder_name' placeholder='Enter folder name' required>
      </div>    
    </div>


      </div>
      <div class='modal-footer'>
        <button type='submit' class='btn btn-primary'>Create</button>
        </form>
        <button type='button' class='btn btn-default' data-dismiss='modal'>Cancel</button>
      </div>
    </div>
  </div>
</div>


</div>
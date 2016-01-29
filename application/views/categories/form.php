

<form>
    <div class="form-group">
        <label for="exampleInputEmail1">Email address</label>
        <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email">
    </div>
    <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
    </div>
    <div class="form-group">
        <label for="exampleInputFile">File input</label>
        <input type="file" id="exampleInputFile">
<!--        <div id="${fileID}" class="uploadify-queue-item preview_container">-->
<!--            <a type="button" href="javascript:$(\'#${instanceID}\').uploadify(\'cancel\', \'${fileID}\')" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></a>-->
<!--            <div class="preview">-->
<!---->
<!--            </div>-->
<!--            <span class="fileName">${fileName} (${fileSize})</span><span class="data"></span>-->
<!--            <div class="uploadify-progress"><div class="uploadify-progress-bar" style="width: 100%;"><!--Progress Bar--></div></div>-->
<!--        </div>-->
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox"> Check me out
        </label>
    </div>
    <div id="progress"></div>
    <a href="<?=base_url($this->module)?>" class="btn btn-default">Cancel</a>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

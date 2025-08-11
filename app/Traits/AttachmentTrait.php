<?php

namespace App\Traits;
use App\CustomClasses\NTLM\NTLMSoapClient;
use App\Traits\WebServicesTrait;
use App\Models\Attachment;

trait AttachmentTrait
{
    use WebServicesTrait;
    //
    public function uploadAttachment($data){
        // $pKey = str_replace("/","_",$data['pKey']);
        $pKey = $data['pKey'];
        $pKey2 = isset($data['pKey2'])? str_replace("/","_",$data['pKey']):null;
        $file = $data['file'];
        $description = $data['description']."_".$pKey;
        $fileNameWithExtension = $file->getClientOriginalName();
        $fileNameOnly = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
        $fileExtensionOnly = $file->getClientOriginalExtension();
        $fileNameToStore = $description;
        $b64File = base64_encode(file_get_contents($file));
        $filePath = config('app.storagePath')."\\".$description.'.'.$fileExtensionOnly;
        //save file to server
        $file = fopen($filePath,'w');
        fwrite($file,base64_decode($b64File));
        fclose ($file);
        //upload to db
        $service = $this->MySoapClient(config('app.cuStaffPortal'));
        $params = new \stdClass();
        $params->docNo = $pKey;
        $params->docNo2 = $pKey2 != null? $pKey2:'';
        $params->docNoFieldId = $data['tableDesc']['pKeyID'];
        $params->fileName = $filePath;
        $params->file = $b64File;
        $params->tableID = $data['tableDesc']['tableID'];
        $storedFile = $this->odataClient()->from(Attachment::wsName())
            ->where('Table_ID',$data['tableDesc']['tableID'])
            ->where('No',$pKey)
            ->where('Name',str_replace("/","_",$description))
            ->first();
        if($storedFile != null){
            $this->deleteAttachment($data);
        }
        $result = $service->UploadDocumentAttachment($params);
        return true;
	}
    public function deleteAttachment($data){
        $service2 = $this->MySoapClient(config('app.cuStaffPortal'));
        !isset($params2)? $params2 = new \stdClass():'';
        $params2->docNo = $data['pKey'];
        $params2->tableID = $data['tableDesc']['tableID'];
        $params2->docID = $data['pKey'];
        $result2 = $service2->DeleteDocumentAttachment($params2);
        return true;
	}
    public function getAttachment($data){
        $service = $this->MySoapClient(config('app.cuStaffPortal'));
        !isset($params)? $params = new \stdClass():'';
        $params->docNo = $data['pKey'];
        $params->tableID = $data['tableDesc']['tableID'];
        $params->attachmentID = $data['attachmentID'];
        $result = $service->GetDocumentAttachment($params);
        return $result->return_value;
    }
}

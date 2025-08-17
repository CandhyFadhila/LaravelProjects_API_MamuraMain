<?php

namespace App\Helpers;

use App\Models\Document;
use Illuminate\Support\Facades\Log;
use App\Helpers\StorageServerHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentHelper
{
	public static function uploadDocuments(array $files): array
	{
		$documentIds = [];
		$uploadedFiles = StorageServerHelper::uploadToServer($files);

		if (is_array($uploadedFiles) && count($uploadedFiles) > 0) {
			foreach ($uploadedFiles as $uploadedFile) {
				if (is_array($uploadedFile) && isset($uploadedFile['server_file_id'])) {

					$document = Document::create([
						'uploaded_by'        => Auth::id(),
						'verified_by'        => 1,
						'file_id'            => $uploadedFile['server_file_id'],
						'file_name'          => $uploadedFile['server_file_name'],
						'file_path'			 		 => $uploadedFile['server_file_path'],
						'file_url'           => $uploadedFile['server_file_url'],
						'file_mime_type'     => $uploadedFile['server_file_mime_type'],
						'file_size'          => $uploadedFile['server_file_size'],
					]);

					$documentIds[] = $document->id;
					Log::channel('helper_document')->info('| uploadDocuments | - Document uploaded successfully', [
						'file_name' => $uploadedFile['server_file_name'],
						'file_size' => $uploadedFile['server_file_size'],
						'document_id' => $document->id
					]);
				} else {
					Log::channel('helper_document')->error('| uploadDocuments | - Failed to save document. No file_id in response.', [$uploadedFile]);
				}
			}
		} else {
			Log::channel('helper_document')->error('| uploadDocuments | - Invalid upload response format.', [$uploadedFiles]);
		}

		return $documentIds;
	}

	public static function deleteDocuments(array $documentIdsToDelete): void
	{
		$documents = Document::whereIn('id', $documentIdsToDelete)->get();
		$fileIds = $documents->pluck('file_id')->toArray();

		// 1) Soft-delete DB dulu (ikut transaksi caller)
		Document::whereIn('id', $documentIdsToDelete)->delete();

		// 2) Setelah commit baru sentuh storage
		DB::afterCommit(function () use ($fileIds, $documentIdsToDelete) {
			if (empty($fileIds)) return;

			try {
				$res = StorageServerHelper::deleteFromServer($fileIds);
				Log::channel('helper_document')->info('| deleteDocuments | - Deleted on storage.', [
					'file_ids'     => $fileIds,
					'document_ids' => $documentIdsToDelete,
					'result'       => is_array($res) ? $res : ['raw' => $res],
				]);
			} catch (\Throwable $e) {
				Log::channel('helper_document')->error('| deleteDocuments | - Storage delete failed', [
					'file_ids'  => $fileIds,
					'error'     => $e->getMessage(),
				]);
				// (opsional) tandai untuk retry async/job
			}
		});
	}

	public static function deleteDocumentsAndNullify(mixed $model, string $documentField = 'document_id'): void
	{
		$documentIds = is_array($model->{$documentField})
			? $model->{$documentField}
			: json_decode($model->{$documentField}, true);

		if (!empty($documentIds)) {
			$fileIds = Document::whereIn('id', $documentIds)->pluck('file_id')->toArray();

			$model->update([$documentField => null]);

			if (!empty($fileIds)) {
				StorageServerHelper::deleteFromServer($fileIds);

				Log::channel('helper_document')->info('| deleteDocumentsAndNullify | - Deleted documents from storage server.', [
					'file_ids' => $fileIds
				]);
			}

			Document::whereIn('id', $documentIds)->delete();
		}
	}
}

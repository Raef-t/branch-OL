import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/scan/presentation/managers/models/scan_qr_model.dart';

abstract class ScanQrRepository {
  Future<Either<FailureError, ScanQrStudentModel>> scanQr({
    required String qrContent,
  });
}

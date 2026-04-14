import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/scan/data/repositories/scan_qr_repositories.dart';
import '/features/scan/data/services/scan_qr_service.dart';
import '/features/scan/presentation/managers/models/scan_qr_model.dart';

class ScanQrRepositoryImplementation implements ScanQrRepository {
  final ScanQrService scanQrService;
  ScanQrRepositoryImplementation({required this.scanQrService});
  @override
  Future<Either<FailureError, ScanQrStudentModel>> scanQr({
    required String qrContent,
  }) async {
    try {
      final response = await scanQrService.scanQr(qrContent: qrContent);
      final student = ScanQrStudentModel.fromJson(
        json: response.data['student'],
      );
      return Right(student);
    } on DioException catch (e) {
      return Left(ErrorServer.fromDioException(dioException: e));
    } catch (e) {
      return Left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر مسح كيو ار، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}

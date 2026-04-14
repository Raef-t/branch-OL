import 'package:flutter_bloc/flutter_bloc.dart';
import '/features/scan/data/repositories/scan_qr_repositories_implementation.dart';
import '/features/scan/presentation/managers/cubits/scan_qr_state.dart';

class ScanQrCubit extends Cubit<ScanQrState> {
  ScanQrCubit({required this.scanQrRepositoryImplementation})
    : super(ScanQrInitialState());
  final ScanQrRepositoryImplementation scanQrRepositoryImplementation;
  Future<void> scanQr({required String qrContent}) async {
    emit(ScanQrLoadingState());
    final result = await scanQrRepositoryImplementation.scanQr(
      qrContent: qrContent,
    );
    result.fold(
      (failure) {
        emit(
          ScanQrFailureState(errorMessage: failure.errorMessageInFailureError),
        );
      },
      (student) {
        emit(ScanQrSuccessState(student: student));
      },
    );
  }
}

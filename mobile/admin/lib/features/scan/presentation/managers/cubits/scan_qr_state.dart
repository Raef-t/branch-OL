import '/features/scan/presentation/managers/models/scan_qr_model.dart';

abstract class ScanQrState {}

class ScanQrInitialState extends ScanQrState {}

class ScanQrLoadingState extends ScanQrState {}

class ScanQrSuccessState extends ScanQrState {
  final ScanQrStudentModel student;
  ScanQrSuccessState({required this.student});
}

class ScanQrFailureState extends ScanQrState {
  final String errorMessage;
  ScanQrFailureState({required this.errorMessage});
}

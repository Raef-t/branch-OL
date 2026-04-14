import 'package:flutter_bloc/flutter_bloc.dart';
import '/features/profile/data/repositories/edit_photo_employee/photo_employee_repositories_implementation.dart';
import '/features/profile/presentation/managers/cubits/edit_photo_employee/photo_employee_state.dart';

class EmployeePhotoCubit extends Cubit<EmployeePhotoState> {
  final PhotoEmployeeRepositoriesImplementation
  photoEmployeeRepositoriesImplementation;
  EmployeePhotoCubit({required this.photoEmployeeRepositoriesImplementation})
    : super(EmployeePhotoInitialState());
  Future<void> uploadPhoto({
    required int employeeId,
    required String filePath,
  }) async {
    emit(EmployeePhotoLoadingState());
    final result = await photoEmployeeRepositoriesImplementation
        .uploadEmployeePhoto(employeeId: employeeId, filePath: filePath);
    result.fold(
      (failure) {
        emit(
          EmployeePhotoFailureState(
            errorMessageInCubit: failure.errorMessageInFailureError,
          ),
        );
      },
      (photoUrl) {
        emit(EmployeePhotoSuccessState(photoUrlInCubit: photoUrl));
      },
    );
  }
}

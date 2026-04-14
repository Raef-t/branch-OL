abstract class EmployeePhotoState {}

class EmployeePhotoInitialState extends EmployeePhotoState {}

class EmployeePhotoLoadingState extends EmployeePhotoState {}

class EmployeePhotoSuccessState extends EmployeePhotoState {
  final String photoUrlInCubit;
  EmployeePhotoSuccessState({required this.photoUrlInCubit});
}

class EmployeePhotoFailureState extends EmployeePhotoState {
  final String errorMessageInCubit;
  EmployeePhotoFailureState({required this.errorMessageInCubit});
}

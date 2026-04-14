import '/features/profile/presentation/managers/models/edit_first_and_last_name_employee/employee_model.dart';

abstract class EmployeeState {}

class EmployeeInitialState extends EmployeeState {}

class EmployeeLoadingState extends EmployeeState {}

class EmployeeSuccessState extends EmployeeState {
  final EmployeeModel employeeModel;
  EmployeeSuccessState({required this.employeeModel});
}

class EmployeeFailureState extends EmployeeState {
  final String errorMessage;
  EmployeeFailureState({required this.errorMessage});
}

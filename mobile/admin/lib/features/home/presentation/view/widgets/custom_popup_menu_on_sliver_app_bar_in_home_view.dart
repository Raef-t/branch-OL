import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/failure_state_component.dart';
import '/features/home/presentation/managers/cubits/institute_branch/institute_branch_cubit.dart';
import '/features/home/presentation/managers/cubits/institute_branch/institute_branch_state.dart';
import '/features/home/presentation/view/widgets/custom_popup_menu_success_state_in_home_view.dart';

class CustomPopupMenuOnSliverAppBarInHomeView extends StatelessWidget {
  const CustomPopupMenuOnSliverAppBarInHomeView({
    super.key,
    required this.child,
  });
  final Widget child;
  @override
  Widget build(BuildContext context) {
    return BlocBuilder<InstituteBranchCubit, InstituteBranchState>(
      builder: (context, state) {
        if (state is InstituteBranchSuccessState) {
          return CustomPopupMenuSuccessStateInHomeView(
            listOfInstituteBranchModel:
                state.listOfInstituteBranchModelInCubit,
            selectedInstituteBranchModel:
                state.selectedInstituteBranchModelInCubit,
            child: child,
          );
        } else if (state is InstituteBranchFailureState) {
          return FailureStateComponent(
            errorText: state.errorMessageInCubit,
            onPressed: () =>
                context.read<InstituteBranchCubit>().getInstituteBranches(),
          );
        } else {
          return child;
        }
      },
    );
  }
}

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/sized_boxs/widths.dart';
import '/features/auth/presentation/managers/cubits/auth_cubit.dart';
import '/features/auth/presentation/managers/cubits/auth_state.dart';
import '/features/home/presentation/view/widgets/custom_profile_miss_image_home_view.dart';
import '/features/home/presentation/view/widgets/custom_two_texts_details_to_teacher_and_hand_image_home_view.dart';

class CustomTrailingCupertinoNavigationbarInHomeView extends StatelessWidget {
  const CustomTrailingCupertinoNavigationbarInHomeView({
    super.key,
    required this.userName,
    required this.userPhoto,
  });
  final String userName, userPhoto;
  @override
  Widget build(BuildContext context) {
    return BlocBuilder<AuthCubit, AuthState>(
      builder: (context, state) {
        if (state is AuthSuccessState) {
          final userModel = state.authModelInCubit.userModel;
          return OnlyPaddingWithChild.right20(
            context: context,
            child: Row(
              children: [
                CustomTwoTextsDetailsToTeacherAndHandImageHomeView(
                  userModel: userModel,
                  userName: userName,
                ),
                Widths.width8(context: context),
                CustomProfileMissImageHomeView(
                  userModel: userModel,
                  userPhoto: userPhoto,
                ),
              ],
            ),
          );
        } else if (state is AuthFailureState) {
          return FailureStateComponent(errorText: state.errorMessageInCubit);
        } else {
          return const CircleLoadingStateComponent();
        }
      },
    );
  }
}
